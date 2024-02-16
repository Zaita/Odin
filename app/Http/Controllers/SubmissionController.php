<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\ApprovalFlow;
use App\Models\Configuration;
use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\Submission;
use App\Models\Risk;
use App\Models\TaskSubmission;
use App\Models\User;
use App\Models\SubmissionCollaborator;

use App\Objects\QuestionnaireObject;

use App\Http\Requests\CollaboratorAddRequest;

class SubmissionController extends Controller
{
  /**
   * GET: /start/{pillarId}
   * 
   * Load the key information screen for a pillar so the user may 
   * read the content and click start
   */
  public function information(Request $request, $pillarId) {
    Log::Info("pillar.information(${pillarId})");
    $pillar = Pillar::findOrFail($pillarId);    
    $config = json_decode(Configuration::GetSiteConfig()->value);    

    return Inertia::render('Start', [
      'siteConfig' => $config,
      'pillar' => $pillar,
    ]);   
  }

  /**
   * This method will take the pillarId and create a new submission
   * with the contents of the questionnaire and pillar. Because we bind a 
   * submission to the version of the pillar at the point of starting it,
   * well need to copy all that information into the submission entry.
   * 
   * Once this is done, we route to the inProgress page with the new UUID
   * of the submission so that the questions can be displayed to the user.
   */
  public function start(Request $request, $pillarId) {
    $pillar = Pillar::findOrFail($pillarId);      
    $questionnaire = Questionnaire::with([
      "questions" => function(Builder $q) {$q->orderBy('sort_order');},
      "questions.inputFields",
      "questions.inputFields.checkbox_options",
      "questions.actionFields"      
      ])->findOrFail($pillar->questionnaire_id);

    Log::Info($questionnaire);
    
    $approvalFlow = ApprovalFlow::findOrFail($pillar->approval_flow_id);

    $user = $request->user();
    $s = new Submission();
    $s->initAndSave($pillar, $user, $questionnaire);

    return Redirect::route('submission.inprogress', ['uuid' => $s->uuid]);   
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function view(Request $request, $uuid) {
    $submission = Submission::where('uuid', $uuid)->first();
    switch($submission->status) {
      case "in_progress":
        return Redirect::route('submission.inprogress', ['uuid' => $uuid]);   
      case "submitted":
      case "waiting_for_approval":
      case "approved":
      case "denied":
        return Redirect::route('submission.submitted', ['uuid' => $uuid]);   
    }    
  }
  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function inProgress(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }

    return Inertia::render('Submission/InProgress', [
      'siteConfig' => $config,
      'submission' => $submission,
    ]);  
  }

  /**
   * Update our submission with new answers from the user.
   */
  public function update(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }
    
    $errors = array();

    // Populate an array with the user's answers
    $actionValue = null;
    $userAnswers = $request->input('answers', []);
    $newAnswerValues = array();
    foreach($userAnswers as $field => $value) {
      $newAnswerValues[$field] = $value; 
      // if the answer has a button action, we need to
      // check for a goto outcome and update the not_applicable
      // answers
      if ($field == "action") {
        $actionValue = $value; // Answer has an action type
      }
    }

    // Set the answers on our submission
    $currentQuestion = $request->input('question', '');
    $lastQuestion = $currentQuestion;
    $submissionQuestions = json_decode($submission->questionnaire_data);
    $submissionAnswers = json_decode($submission->answer_data);
    if ($submission->validateAnswers($errors, $currentQuestion, $submissionQuestions, $newAnswerValues)) {
      $submission->setAnswers($submissionAnswers, $currentQuestion, $newAnswerValues); // Modified $submissionAnswers

      /**
       * Now we need to check if we're jumping forward more than one question. This is caused
       * by the goto action on a button.
       */
      if ($actionValue != null) {
        $questionnaireData = json_decode($submission->questionnaire_data);
        foreach ($questionnaireData as $question) {
          if ($question->title == $currentQuestion) {
            foreach($question->action_fields as $actionField) {
              if ($actionField->label == $actionValue) {
                if ($actionField->action_type != "goto") {
                  break 2; // Stop both loops
                }
                // We have a go to
                $submission->handleGoto($submissionAnswers, $currentQuestion, $actionField->goto_question_title);
                $lastQuestion =  $actionField->goto_question_title;
              }
            }
            break;
          }
        }
      }
      
      // Update last question and save our submission
      $submissionAnswers->last_question = $lastQuestion;
      $submission->answer_data = json_encode($submissionAnswers);
      $submission->save();
    } // if ($submission->validateAnswers($errors, $currentQuestion, $newAnswerValues)

    // If we answered last question, go to review.
    if (count($errors) == 0 && $submission->isLastQuestion($currentQuestion, $submissionQuestions)) {
      Log::Info("Redirecting");
      return Redirect::route('submission.review', ['uuid' => $submission->uuid]);
    } else {
      return Inertia::render('Submission/InProgress', [
        'siteConfig' => $config,
        'submission' => $submission,
        'answer_data' => $submission->answer_data,
        'errors' => $errors
      ]);  
    }
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function review(Request $request, $uuid) {
    Log::Info("Reviewing a submission");
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }

    $submission->calculateRiskScore();

    return Inertia::render('Submission/Review', [
      'siteConfig' => $config,
      'submission' => $submission,
      'risks' => Risk::all(),
      'riskTitle' => "Final risk ratings"
    ]);  
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function submit(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }

    if (!$submission->submit()) {
      Log::Info("Error when submitting");
      return back()->withInput()->withErrors($submission->errors); 
    }

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]);
  }

  /**
   * 
   */
  public function submitted(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::with(['approval_stages'])
      ->where('uuid', $uuid)->first();
    // TODO: If not right status, redirect elsewhere
    $tasks = TaskSubmission::where(['submission_id' => $submission->id])->get();

    $user = $request->user();
    $secureToken = $request->input('secure_token', '');

    Log::Info("--> Submitted Load");
    $isAnApprover = $submission->isAnApprover($user);
    Log::Info(sprintf("- Is an approver? %b", $isAnApprover));
    $canBeAssigned = $submission->canAssignUser($user);
    Log::Info(sprintf("- Can be assigned? %b", $canBeAssigned));
    $canApproveWithType = $submission->canApproveWithType($user, $secureToken);
    Log::Info(sprintf("- Can approve with type? %s", $canApproveWithType));

    $collaborators = SubmissionCollaborator::with('user')->where(['submission_id' => $submission->id])->get();

    return Inertia::render('Submission/Submitted', [
      'siteConfig' => $config,
      'submission' => $submission,
      'collaborators' => $collaborators,
      'tasks' => $tasks,
      'status' => $submission->nice_status(),
      'is_an_approver' => $isAnApprover,
      'can_be_assigned' => $submission->canAssignUser($user),
      'can_approve_with_type' => $submission->canApproveWithType($user, $secureToken)
    ]); 
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function edit(Request $request, $uuid) {
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }

    if ($submission->status != "submitted") {
      return redirect()->route("error")->withErrors(["error" => "Submission cannot be edited"]);
    }

    $submission->status = "in_progress";
    $submission->save();
    return Redirect::route('submission.inprogress', ['uuid' => $submission->uuid]);
  }

  /**
   * POST /submitforapproval/{uuid}
   */
  public function submitForApproval(Request $request, $uuid) {
    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      return redirect()->route("error")->withErrors(["error" => "Could not find that submission"]);
    }

    $tasks = TaskSubmission::where(['submission_id' => $submission->id])->get();
    if (!$submission->submitForApproval()) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]); 
  }

  /**
   * POST /submission/assigntome/{uuid}
   * 
   * Assign the submission to the current user
   */
  public function assignToMe(Request $request, $uuid) {
    $submission = Submission::where('uuid', $uuid)->first();
    $user = $request->user();
    $secureToken = $request->input('secure_token', '');

    if (!$submission->assignToUser($user, $secureToken)) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]); 
  }

  /**
   * 
   */
  public function sendBackForChanges(Request $request, $uuid) { 
    $submission = Submission::where('uuid', $uuid)->first();
    $user = $request->user();

    $submission->status = "submitted";
    $submission->save();
    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]); 
  }

  /**
   * Handle /approve/{uuid}
   * where the current user is attempting to approve part of the submission
   */
  public function approve(Request $request, $uuid) { 
    $submission = Submission::where('uuid', $uuid)->first();
    $user = $request->user();

    if (!$submission->approve($user)) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]); 
  }
  
  public function deny(Request $request, $uuid) { }

  /**
   * POST /submission/{uuid}/collaborator/add
   * 
   * Add a collaborator to our submission. A collaborator is someone who can
   * complete tasks on behalf of the submitter.
   */
  public function addCollaborator(CollaboratorAddRequest $request, $uuid) {
    $errors = array();

    $submission = Submission::where(['uuid' => $uuid, 'submitter_id' => $request->user()->id])->first();
    if ($submission == null) {
      $errors["email"] = "You do not have permission to add collaborators";
    
    } else {
      $email = $request->input('email');
      $user = User::where(['email' => $email])->first();
      if ($user == null) {
        $errors["email"] = "User could not be found. Have they logged in before?";
      } else {
        $s = SubmissionCollaborator::firstOrCreate(['submission_id' => $submission->id, 'user_id' => $user->id]);
      }
    }

    return back()->withInput()->withErrors($errors); 
  }

  /**
   * Handle the user loading the task url /task/{uuid} 
   * based on task status
   */
  public function task_index(Request $request, $uuid) { 
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();
    
    // Task is ready to start.
    Log::Info("Task Status: $task->status (type: $task->task_type)");
    if ($task->status == "ready_to_start") {
      if (!$submission->canWorkOnTask($request->user())) {
        return back()->withInput()->withErrors($submission->errors); 
      }
      
      if ($task->show_information_screen) {
        return Inertia::render("Submission/Task/Information", [
          'siteConfig' => $config,
          'submission' => $submission,
          'task' => $task,
        ]);
      } 
    
      // Skip the show information screen and set task to in_progress
      $task->status = "in_progress";
      $task->save();  
      return Redirect::route('submission.task.inprogress', ['uuid' => $task->uuid]); 

    } else if ($task->status == "in_progress") {
      return $this->task_inprogress($request, $uuid);
    } else if ($task->status == "in_review") {
      Redirect::route('submission.task.review', ['uuid' => $task->uuid]); 
    } else if ($task->status == "waiting_for_approval") {
      Redirect::route('submission.task.submitted', ['uuid' => $task->uuid]); 
    } else if ($task->status == "complete" || $task->status == "approved" || $task->status == "denied") {
      return Redirect::route('submission.task.view', ['uuid' => $task->uuid]); 
    }
  }

  /**
   * Handle POST /task/start/{uuid} 
   * This is called when either:
   * a) the user clicks on a task from the submission submitted screen
   *    and the task does not have the display_information_screen flag; OR
   * b) the user clicks start from the task information screen after
   *    it has been clicked from the submission's submitted screen.
   * 
   * This method will update the Task submission entry to "in_progress"
   * and redirect.
   */
  public function task_start(Request $request, $uuid) {     
    $task = TaskSubmission::where(['uuid' => $uuid])->first();

    // Verify user can work on this submission (is submitter or collaborator)
    $submission = Submission::where('id', $task->submission_id)->first();
    if (!$submission->canWorkOnTask($request->user())) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    $task->status = "in_progress";
    $task->save();        
    return Redirect::route('submission.task.inprogress', ['uuid' => $task->uuid]);  
  }

  /**
   * Handle GET /task/inprogress/{uuid}
   * This is used when a task on a submission is in progress and the user
   * is entering data
   */
  public function task_inprogress(Request $request, $uuid) {
    Log::Info("Loading in_progress task $uuid");
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();

    // Verify user can work on this submission (is submitter or collaborator)
    $submission = Submission::where('id', $task->submission_id)->first();
    if (!$submission->canWorkOnTask($request->user())) {
      Log::Error("User $user->email cannot work on submission $submission->uuid");
      return back()->withInput()->withErrors($submission->errors); 
    }

    if ($task->task_type == "questionnaire" || $task->task_type == "risk_questionnaire") {
      return Inertia::render('Submission/Task/InProgress', [
        'siteConfig' => $config,
        'submission' => $submission,
        'task' => $task,
      ]);     
    } else {
      return Inertia::render('Submission/Task/SecurityRiskAssessment', [
        'siteConfig' => $config,
        'submission' => $submission,
        'task' => $task,
      ]);         
    }
  }

  /**
   * Handle AJAX callback to give new answer data to our task
   */
  public function task_update(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();

    // Verify user can work on this submission (is submitter or collaborator)
    $submission = Submission::where('id', $task->submission_id)->first();
    if (!$submission->canWorkOnTask($request->user())) {
      return back()->withInput()->withErrors($submission->errors); 
    }
    
    $questionnaire = new QuestionnaireObject($task->task_data, $task->answer_data, true);

    // Get user inputs
    $actionValue= null;
    $newAnswerValues = array();
    $currentQuestion = $request->input('question', '');
    $userAnswers = $request->input('answers', []);
    foreach($userAnswers as $field => $value) {
      $newAnswerValues[$field] = $value; 
      if ($field == "action") {
        $actionValue = $value; // Answer has an action type
      }
    }
    $userAnswers = $newAnswerValues;

    if ($questionnaire->validateAnswers($currentQuestion, $userAnswers) && $questionnaire->processAnswers($currentQuestion, $userAnswers, $actionValue)) {
      $task->answer_data = json_encode($questionnaire->answers);
      $task->save();
    }

    if (count($questionnaire->errors) == 0 && $questionnaire->isLastQuestion($currentQuestion)) {
      return Redirect::route('submission.task.review', ['uuid' => $task->uuid]);    
    } else {
      return Inertia::render('Submission/Task/InProgress', [
        'siteConfig' => $config,
        'task' => $task,
        'submission' => $submission,
        'answer_data' => $task->answer_data,
        'errors' => $questionnaire->errors
      ]); 
    }
  }

  /**
   * Handle GET /task/review/{uuid}
   * 
   * This is called when a task completes it's final question. The user is then asked to review the
   * answers and complete the task.
   */
  public function task_review(Request $request, $uuid) { 
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();

    // Verify user can work on this submission (is submitter or collaborator)
    $submission = Submission::where('id', $task->submission_id)->first();
    if (!$submission->canWorkOnTask($request->user())) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    $questions = json_decode($task->task_data);
    $questions = $questions->questionnaire->questions;

    return Inertia::render('Submission/Task/Review', [
      'siteConfig' => $config,
      'submission' => $submission,
      'questions' => $questions,
      'task' => $task,
    ]); 
  }

  /**
   * Handle the POST /task/submit/{uuid} 
   * 
   * This is called from the /task/review/{uuid} page once the user
   * has reviewed their responses and is happy with them.
   */
  public function task_submit(Request $request, $uuid) { 
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    // Verify user can work on this submission (is submitter or collaborator)
    $submission = Submission::where('id', $task->submission_id)->first();
    if (!$submission->canWorkOnTask($request->user())) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    $task->submitter_id = $request->user()->id;
    $task->submitter_name = $request->user()->name;
    $this->submitter_email = $request->user()->email;
    $task->status = "complete";
    $task->save();

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]);    
  }

  /**
   * GET /task/submitted/{uuid}
   * 
   * This is where we can see the task once it has been completed by the submitter or
   * a collaborator. Generally this is used to view answers, but it's also the screen
   * used to approve tasks by a task approver
   */
  public function task_submitted(Request $request, $uuid) { 
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();

    $questions = json_decode($task->task_data);
    $questions = $questions->questionnaire->questions;

    return Inertia::render('Submission/Task/Submitted', [
      'siteConfig' => $config,
      'submission' => $submission,
      'questions' => $questions,
      'task' => $task,
    ]); 
  }

    /**
   * GET /task/view/{uuid}
   * 
   * This is where we can see the task once it has been completed by the submitter or
   * a collaborator. This is used as a view only, not for approving
   */
  public function task_view(Request $request, $uuid) { 
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();

    $questions = json_decode($task->task_data);
    $questions = $questions->questionnaire->questions;

    return Inertia::render('Submission/Task/View', [
      'siteConfig' => $config,
      'submission' => $submission,
      'questions' => $questions,
      'task' => $task,
    ]); 
  }
}
