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
use App\Models\Task;
use App\Models\TaskSubmission;

use App\Objects\QuestionnaireObject;

class SubmissionController extends Controller
{
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
      "questions.actionFields",
      ])->findOrFail($pillar->questionnaire_id);
    
    $approvalFlow = ApprovalFlow::findOrFail($pillar->approval_flow_id);

    $user = $request->user();
    $s = new Submission();
    $s->user_id = $user->id;
    $s->submitter_name = $user->name;
    $s->submitter_email = $user->email;
    $s->pillar_name = $pillar->name;
    $s->questionnaire_data = $questionnaire;
    $s->pillar_data = $pillar;
    $s->approval_flow_data = $approvalFlow;
    $s->save();

    return Redirect::route('submission.inprogress', ['uuid' => $s->uuid]);   
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function view(Request $request, $uuid) {
    $submission = Submission::where('uuid', $uuid)->first();
    if ($submission->status == "in_progress") {
      return Redirect::route('submission.inprogress', ['uuid' => $uuid]);   
    } else if ($submission->status == "submitted") {
      return Redirect::route('submission.submitted', ['uuid' => $uuid]);   
    }
  }
  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function inProgress(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where('uuid', $uuid)->first();

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
    $submission = Submission::where('uuid', $uuid)->first();
    
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
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where('uuid', $uuid)->first();

    return Inertia::render('Submission/Review', [
      'siteConfig' => $config,
      'submission' => $submission,
    ]);  
  }

  /**
   * This method will load the questionnaire submission for the current uuid.
   */
  public function submit(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where('uuid', $uuid)->first();

    if (!$submission->submit()) {
      return back()->withInput()->withErrors($submission->errors); 
    }
    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]);
  }

  /**
   * 
   */
  public function submitted(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where('uuid', $uuid)->first();
    // TODO: If not right status, redirect elsewhere
    $tasks = TaskSubmission::where(['submission_id' => $submission->id])->get();

    return Inertia::render('Submission/Submitted', [
      'siteConfig' => $config,
      'submission' => $submission,
      'tasks' => $tasks,
      'status' => $submission->nice_status()
    ]); 
  }

  /**
   * POST /submitforapproval/{uuid}
   */
  public function submitForApproval(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::where('uuid', $uuid)->first();
    $tasks = TaskSubmission::where(['submission_id' => $submission->id])->get();

    if (!$submission->submitForApproval()) {
      return back()->withInput()->withErrors($submission->errors); 
    }

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]); 
  }

  /**
   * Handle the user loading the task url /task/{uuid} 
   * based on task status
   */
  public function task_index(Request $request, $uuid) { 
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();
        
    // if ($task->status == "ready_to_start") {
      return Inertia::render("Submission/Task/Information", [
        'siteConfig' => $config,
        'submission' => $submission,
        'task' => $task,
      ]);
    // }
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
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();

    return Inertia::render('Submission/Task/InProgress', [
      'siteConfig' => $config,
      'submission' => $submission,
      'task' => $task,
    ]);     
  }

  /**
   * Handle AJAX callback to give new answer data to our task
   */
  public function task_update(Request $request, $uuid) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = TaskSubmission::where(['uuid' => $uuid])->first();
    $submission = Submission::where('id', $task->submission_id)->first();
    
    $questionnaire = new QuestionnaireObject($task->task_data, $task->answer_data, true);

    // Get user inputs
    $actionValue= null;
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
    $submission = Submission::where('id', $task->submission_id)->first();

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
    $submission = Submission::where('id', $task->submission_id)->first();

    $task->status = "complete";
    $task->save();

    return Redirect::route('submission.submitted', ['uuid' => $submission->uuid]);    
  }


  public function task_submitted(Request $request, $uuid) { 
    // $config = json_decode(Configuration::GetSiteConfig()->value);
    // $submission = Submission::where('uuid', $uuid)->first();
    // // TODO: If not right status, redirect elsewhere
    // $tasks = TaskSubmission::where(['submission_id' => $submission->id])->get();

    // return Inertia::render('Submission/Submitted', [
    //   'siteConfig' => $config,
    //   'submission' => $submission,
    //   'tasks' => $tasks,
    // ]); 
  }
}
