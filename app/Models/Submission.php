<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\ApprovalFlow;
use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\SubmissionApprovalFlowStage;
use App\Models\TaskSubmission;
use App\Models\User;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $fillable = [
        'status',
        'uuid',
        'submitter_name',
        'submitter_email',
        'questionnaire_data',
        'answer_data'
    ];

    protected $appends = [
      'tasks_completed',
      'created_at_short',
      'nice_status'
    ];
  
  public $errors = array();

  public function approval_stages(): HasMany {
    return $this->hasMany(SubmissionApprovalFlowStage::class);
  }

  /**
   * Construct our submission with some default values
   */
  public function __construct(array $attributes = array()) {
    parent::__construct($attributes);
  }

  /**
   * Construct our submission with some default values
   */
  public function initAndSave(Pillar $pillar, User $user, Questionnaire $questionnaire) {
    /**
     * Load parameter information
     */
    $this->uuid = Str::uuid()->toString();
    $this->submitter_id = $user->id;
    $this->submitter_name = $user->name;
    $this->submitter_email = $user->email;
    $this->pillar_name = $pillar->name;
    $this->questionnaire_data = $questionnaire;
    $this->pillar_data = $pillar;
    $this->status = "in_progress";
    $this->save();

    $approvalFlow = ApprovalFlow::with(["stages" => function(Builder $q) {$q->orderBy('stage_order');}
      ])->where(['id' => $pillar->approval_flow_id])->first();

    foreach($approvalFlow->stages as $stage) {
      $subStage = new SubmissionApprovalFlowStage();
      $subStage->initAndSave($stage, $this->id);
    }    
  }

  /**
   * Bind simple attribute for Model to use.
   */
  protected function TasksCompleted() : Attribute {
    $x = function() { 
      if ($this->status == "in_progress") {
        return "-"; // Not ready to show task statuses
      }
  
      $taskList = TaskSubmission::where(["submission_id" => $this->id])->get();
      $complete = 0;
      $total = count($taskList);
      foreach($taskList as $task) {
        if ($task->status == "complete" || $task->status == "approved") {
          $complete++;
        }
      }
      return sprintf("%d/%d", $complete, $total);
    };
    return Attribute::make(
      get: fn (null $value) => $x(),
    );
  }

  /**
   * Get a shortened version of our created_at date for better display to the user.
   */
  protected function CreatedAtShort() : Attribute {
    return Attribute::make(
      get: fn (null $value) => date_format($this->created_at, "d/m/Y"),
    );
  }

  protected function NiceStatus() : Attribute {
    return Attribute::make(
      get: fn (null $value) => $this->nice_status(),
    );
  }

  /**
   * We're overriding the pillar_data method so that we can remove a bunch of fluff
   */
  public function setPillarDataAttribute($pillar) {
    unset($pillar->icon);
    unset($pillar->key_information);
    unset($pillar->key_information);
    unset($pillar->enabled);
    $this->attributes['pillar_data'] = json_encode($pillar);
  }

  /**
   * We're overriding the questionnaire_data method so that we can parse the question information
   * from our questionnaire and insert some extra information. Because the question
   * information is not going to change. We want to add some unique identifiers
   * that can be used for storing the answer information against. This means we can
   * split question and answer data and not have to transfer as much data.
   * 
   * i.e., question data will be static, but answer data will change based on user input.
   */
  public function setQuestionnaireDataAttribute($questionnaire) {

    $answers = [];
    foreach($questionnaire->questions as $question) {
      $newAnswer = array();
      $newAnswer["question"] = $question->title;
      $newAnswer["status"] = "incomplete";
      $newAnswer["data"] = array();
      array_push($answers, $newAnswer);     
    }

    $answerData = array("answers" => $answers);

    $this->attributes['questionnaire_data'] = json_encode($questionnaire->questions);
    $this->attributes['answer_data'] = json_encode($answerData);
  }

  /**
   *
   * @param errors Array to populate with any errors
   * @param currentQuestion Title of current question
   * @param submissionQuestions JSON of all questions in the submission
   * @param newAnswerValues The values we want to validate
   */
  public function validateAnswers(&$errors, $currentQuestion, $submissionQuestions, $newAnswerValues) {
    Log::Info("Validating answers from user");

    // Find the target question in our questionnaire
    $targetQuestion = null;
    foreach($submissionQuestions as $question) {
      if ($question->title == $currentQuestion) {
        $targetQuestion = $question;
        break;
      }
    }

    if ($targetQuestion == null) {
      Log::Emergency("Could not find the question $currentQuestion in the submission");
    }

    // Check if we can skip validation. This is because there
    // are no defined Answer Input Fields on this question
    if (!isset($targetQuestion->input_fields)) {
      return true; // No validation required.
    }

    // Loop over each answerInputField in our questionnaire
    foreach($targetQuestion->input_fields as $inputField) { 
      $label = $inputField->label;

      $value = isset($newAnswerValues[$label]) ? $newAnswerValues[$label] : null;
      if (isset($inputField->required) && $inputField->required && ($value == null || $value == "")) {
        $errors[$label] = "$label is a required field";
        continue;
      }
      if ( (!isset($inputField->required) || !$inputField->required) && ($value == null || $value == "") ) {
        continue;
      }
      if (isset($inputField->min_length) && $inputField->min_length > 0 && (strlen($value) < $inputField->min_length)) {
        $errors[$label] = "$label must be at least $inputField->min_length characters long";
        continue;
      }
      if (isset($inputField->max_length) && $inputField->max_length > 0 && (strlen($value) > $inputField->max_length)) {
        $length = strlen($value);
        $errors[$label] = "$label must not exceed $inputField->max_length, you have $length";
        continue;
      }
      if ($inputField->input_type == "url" && filter_var($value, FILTER_VALIDATE_URL) === false) {
        $errors[$label] = "$label must be a valid URL (incl https://)";
        continue;
      }
      if ($inputField->input_type == "email" && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
        $errors[$label] = "$label must be a valid email address";
        continue;
      }

      /**
       * Assign Reserved Fields
       */
      if (isset($inputField->product_name) && $inputField->product_name) {
        $this->product_name = $value;
      }
      if (isset($inputField->business_owner) && $inputField->business_owner) {
        $this->business_owner = $value;
      }      
      if (isset($inputField->ticket_url) && $inputField->ticket_url) {
        $this->ticket_link = $value;
      }
      if (isset($inputField->releast_date) && $inputField->releast_date) {
        $this->release_date = $value;
      }
    }

    return count($errors) == 0;
  }

  /**
   * Set the answer on a specific question with the target field
   */
  public function setAnswers(&$answerData, $question, $newAnswers) {
    Log::Info("submission.setAnswer($question)");

    $elementsToAdd = array();
    foreach($answerData->answers as $answerEntry) {
      if ($answerEntry->question == $question) {
        $answerEntry->status = "complete";
        $setValues = array();
        foreach($answerEntry->data as $dataEntry) {
          Log::Info(json_encode($dataEntry));
          foreach ($newAnswers as $fieldLabel => $fieldValue) {
            if ($fieldLabel == $dataEntry->field) {
              $dataEntry->value = $fieldValue;
              $setValues[] = $fieldLabel;
            }
          }
        }
        // Add any new values that were not previously set
        foreach ($newAnswers as $fieldLabel => $fieldValue) {        
          if (!in_array($fieldLabel, $setValues)) {
            Log::Info("Adding New Element X");
            $newData = array("field" => $fieldLabel, "value" => $fieldValue);
            array_push($answerEntry->data, $newData);
          }
        }
      }
    }        
  }

  /**
   * Check if a question specific action on our submission is a "goto" action or not.
   * If it is, return the target question for the goto action.
   */
  public function handleGoto(&$answerData, $currentQuestion, $targetQuestion) {
    $startMarking = false;
    foreach($answerData->answers as $answerEntry) {
      if ($answerEntry->question == $currentQuestion) {
        $startMarking = true; // Start marking from next question as not applicable
        continue; 
      }
      if ($startMarking && $answerEntry->question == $targetQuestion) {
        return; // All finished
      }
      if ($startMarking) {
        // mark answer as not_applicable and clear the data
        $answerEntry->status = "not_applicable";
        $answerEntry->data = [];
      }
    }
  }

  public function isLastQuestion($currentQuestion, $submissionQuestions) {
    $index = null;
    for ($x = 0; $x < count($submissionQuestions); $x++) {
      if ($submissionQuestions[$x]->title == $currentQuestion) {
        $index = $x + 1;
        break;
      }
    }

    if ($index != null && $index == count($submissionQuestions)) {
      Log::Info("isLastQuestion: True");
      return true;
    }

    Log::Info("isLastQuestion: False");
    return false;
  }

  /**
   * Handle the user submitting their questionnaire from a Review into a submit 
   */
  public function submit() {
    if ($this->status == "submitted") {
      $this->errors["error"] = "Submission has been submitted already";
      return false;
    }
    /**
     * Step 1: Create Task Submissions for any tasks that are assigned to the Pillar
     */
    $pillarData = json_decode($this->pillar_data);
    $tasks = json_decode($pillarData->tasks);
    foreach($tasks as $task) {
      $taskObj = Task::where(["name" => $task->name])->first();
      Log::Info($taskObj->id);

      if ($taskObj->type == "questionnaire") {
        $questionnaire = Questionnaire::with([
          "questions" => function(Builder $q) {$q->orderBy('sort_order');},
          "questions.inputFields" => function(Builder $q) {$q->orderBy('sort_order');},
          "questions.actionFields" => function(Builder $q) {$q->orderBy('sort_order');},
          ])->findOrFail($taskObj->task_object_id);

        $taskObj->questionnaire = $questionnaire;
      }

      $taskSubmission = TaskSubmission::firstOrNew(["name" => $taskObj->name, "submission_id" => $this->id]);
      $taskSubmission->name = $taskObj->name;
      $taskSubmission->submission_id = $this->id;
      $taskSubmission->task_type = $taskObj->type;
      $taskSubmission->task_data = $taskObj;
      $taskSubmission->save();
    }

    $this->status = "submitted";
    $this->save();

    return true;
  }

  /**
   * Send this submission through for approval. 
   * We first need to check conditions to ensure all tasks have been completed.
   */
  public function submitForApproval() {
    if ($this->status != "submitted") {
      $this->errors["error"] = "Submission has already been sent for approval";
      return false;
    }
    if ($this->task_count() != $this->completed_task_count()) {
      $this->errors["error"] = "Not all tasks have been completed";
      return false;
    }

    $this->approval_stage = 0;
    $this->status = "waiting_for_approval";
    $this->save();
    return true;
  }

  /**
   * Return a nice status for our submission
   */
  public function nice_status() {
    $status = $this->status;
    if ($status == "in_progress") {
      return "In progress";
    
    } else if ($status == "submitted") {
      $taskList = TaskSubmission::where(["submission_id" => $this->id])->get();
      $complete = 0;
      $total = count($taskList);
      foreach($taskList as $task) {
        if ($task->status == "complete" || $task->status == "approved") {
          $complete++;
        }
      }

      if ($complete < $total) {
        return "Tasks to complete";
      }
      
      return "Ready to submit";
    
    } else if ($status == "waiting_for_approval") {
      return "Waiting for approval";
    } else if ($status = "approved") {
      return "Approved";
    }

    return "-";
  }

  /**
   * Get the total number of tasks assigned to this submission
   */
  protected function task_count() {
    $taskList = TaskSubmission::where(["submission_id" => $this->id])->get();
    return count($taskList);
  }

  /**
   * Get the number of tasks that have been completed or approved. These tasks
   * are considered finished and will determine if the submission can be
   * sent for approval or not.
   */
  protected function completed_task_count() {
    $taskList = TaskSubmission::where(["submission_id" => $this->id,])
      ->whereIn('status', ['complete', 'approved'])->get();
    return count($taskList);
  }

  /**
   * Determine if the user is someone who can be an approver or endorser of the submission.
   * They either have to 
   * A. Have the correct secure token as a URL parameter; OR
   * B. Be in part of the approval chain
   */
  public function IsAnApprover($user) : bool {
    if ($this->status != "waiting_for_approval" || $user->id == $this->submitter_id) {
      Log::Info("User cannot approve their own submissions");
      return false; // Cannot endorse or approve your own submissions
    }

    for ($i = 0; $i < count($this->approval_stages); $i++) {
      // check if user can approve this yet, or is it too early
      // because of the approval stages
      if ($i > $this->approval_stage) {
        return false;
      }

      $stage = $this->approval_stages[$i];
      
      if ($stage->status != null && $stage->status != "") // Already approved/endorsed or not
        continue;

      // If the user a member of the target group?
      if ($stage->type == "group" && $user->isInGroup($stage->target)) {
        return true;
      }
      if ($stage->type == "user" && $user->email == $stage->target) {
        return true;
      }
      if ($stage->type == "business_owner" && $this->business_owner != null && $this->business_owner == $user->email) {
        return true;
      }
    }

    Log::Info("Submission.isAnApprover - No matches");
    return false; // no matches
  }

  /**
   * Determine if the current user can be assigned to the submission or not.
   * 
   * This will determine if they are an approver, but also check to ensure
   * they have not, nor has anybody else, been assigned to the submission
   */
  public function canAssignUser($user) {
    if ($this->status != "waiting_for_approval" || $user->id == $this->submitter_id) {
      return false; // Cannot endorse or approve your own submissions
    }

    for ($i = 0; $i < count($this->approval_stages); $i++) {
      // check if user can approve this yet, or is it too early
      // because of the approval stages
      if ($i > $this->approval_stage) {
        return false;
      }

      $stage = $this->approval_stages[$i];
      // Only assign if the type is group, other types are specific people and don't need to be assigned
      // Also only allow assignment if it's not already assigned
      if ($stage->type == "group" && $user->isInGroup($stage->target) && $stage->assigned_to_user_email == null) {
        return true;
      }     
    }

    return false;
  }

  /**
   * Assign this submission to the user passed in as a parameter. Assignment of a submission to a
   * user ensures that no other user can approve or endorse this submission. Approval/Endorsement
   * can only be done by the person who is currently assigned.
   */
  public function assignToUser($user) {
    if ($user->id == $this->user_id || $user->email == $this->submitter_email) {
      Log::Info(sprintf("User %s is trying to assign a submission to themself", $user->email));
      return false;
    }

    if (!$this->canAssignUser($user)) {
      $this->errors["error"] = "You do not have permission to assign this submission to yourself";
      return false;
    }

    for ($i = 0; $i < count($this->approval_stages); $i++) {
      // check if user can approve this yet, or is it too early
      // because of the approval stages
      if ($i > $this->approval_stage) {
        return false;
      }

      $stage = $this->approval_stages[$i];
      // Only assign if the type is group, other types are specific people and don't need to be assigned
      // Also only allow assignment if it's not already assigned
      if ($stage->type == "group" && $user->isInGroup($stage->target) && $stage->assigned_user_email == null) {
        $this->approval_stages[$i]->assigned_to_user_id = $user->id;
        $this->approval_stages[$i]->assigned_to_user_name = $user->name;
        $this->approval_stages[$i]->assigned_to_user_email = $user->email;
        // $approver->assigned_at = date("Y-m-d H:i:s"); TODO: Add later
        $this->approval_stages[$i]->save();
        return true;
      }     
    }

    return false; // no matches
  }

  /**
   * Check if this submission is assigned to me.
   * 
   * 
   */
  public function canApproveWithType($user, $secureToken) {
    if ($this->status != "waiting_for_approval" || $user->id == $this->submitter_id) {
      return false; // Cannot endorse or approve your own submissions
    }

    for ($i = 0; $i < count($this->approval_stages); $i++) {
      // check if user can approve this yet, or is it too early
      // because of the approval stages
      if ($i > $this->approval_stage) {
        return false;
      }

      $stage = $this->approval_stages[$i];

      if ($stage->status != null && $stage->status != "") {
        continue;
      }
      
      // If the user a member of the target group?
      if ($stage->type == "group" && $stage->assigned_to_user_id == $user->id) {
        return $stage->approval_type;
      }
      if ($stage->type == "user" && $user->email == $stage->target) {
        return $stage->approval_type;
      }
      if ($stage->type == "business_owner" && $this->business_owner != null && $this->business_owner == $user->email) {
        return $stage->approval_type;
      }
      // TODO: Add Secure Token
    }

    return false; // no matches
  }

  /**
   * Approve the submission with the specified user. This will go through all of the
   * approval stages and mark each of them as approved or endorsed based on the persons
   * current role in the system and to the submission.
   * 
   * There are some general rules:
   * 1. The submitter of the submission cannot approve the submission
   * 2. Collaborators can approve the submission
   * 3. A person may hold multiple endorsement and approval roles. They will all be approved at once.
   */
  public function approve($user) {
    if ($this->status == "approved" || $this->status == "denied") {
      $this->errors["error"] = "Cannot approve a submission that has been fully approved or denied";
      return false;
    }
    if ($user->id == $this->submitter_id) {
      $this->errors["error"] = "You cannot approve your own submissions";
      return false; // Cannot endorse or approve your own submissions
    }
    if ($this->status != "waiting_for_approval") {
      $this->errors["error"] = "Submission is not waiting for an approval from you";
      return false; 
    }

    $approveSuccessful = false;

    $approve = function(&$stage) use ($user, &$approveSuccessful) {
      $stage->approved_by_user_id = $user->id;
      $stage->approved_by_user_name = $user->name;
      $stage->approved_by_user_email = $user->email;
      $stage->status = $stage->approval_type == "approval" ? "approved" : "endorsed";
      $stage->save();      
      $approveSuccessful = true;
    };

    
    for ($i = 0; $i < count($this->approval_stages); $i++) {
      // check if user can approve this yet, or is it too early
      // because of the approval stages
      if ($i > $this->approval_stage) {
        break;
      }

      $stage = $this->approval_stages[$i];
      // If the user a member of the target group?
      if ($stage->type == "group" && $stage->assigned_to_user_id == $user->id) {
        $approve($stage);
      }
      if ($stage->type == "user" && $user->email == $stage->target) {
        $approve($stage);
      }
      if ($stage->type == "business_owner" && $this->business_owner != null && $this->business_owner == $user->email) {
        $approve($stage);
      }
      // TODO: Add Secure Token
    }

    if ($approveSuccessful) {
      Log::Info("Submission approve was successful, incrementing approval stage");
      $this->approval_stage++;
      $this->handleApprovalStageChange();
      $this->save();
    } else {
      $this->errors["error"] = "You do not have permission to approve this submission";
    }

    return $approveSuccessful;
  }

  /**
   * Handle the tasks we need to do when we are changing an approval stage.
   */
  protected function handleApprovalStageChange() {
    /**
     * Firstly, we want to check the "wait_for_approval" flag. If this flag is false,
     * then we can continue through this approval stage and enable the next one. This can
     * be used where you want an out-of-band approval or endorsement
     */
    for ($i = $this->approval_stage; $i < count($this->approval_stages); $i++) {
      $stage = $this->approval_stages[$i];
      if (isset($stage->wait_for_approval) && !$stage->wait_for_approval) {
        Log::Info("Moving an extra approval stage forward because of wait_for_approval flag");
        $this->approval_stage++;
        $this->handleApprovalStageChange();
        return;
      }
    }

    Log::Info(sprintf("--Current Approval Stage: %d", $this->approval_stage));

    /**
     * If we've done the last stage, then we need to determine if this submission is approved
     * or not.
     */
    if ($this->approval_stage >= count($this->approval_stages)) {
      /**
       * Mark any approval stages not yet completed as expired
       */
      for ($i = 0; $i < count($this->approval_stages); $i++) {
        $stage = $this->approval_stages[$i];
        if ($stage->status == null || $stage->status == "") {
          $this->approval_stages[$i]->status = $stage->approval_type == "approval" ? "approval_expired" : "endorsement_expired";
          $this->approval_stages[$i]->save();
        }
      }
      $this->status = "approved";
      Log::Info("Submission has been approved");
      // Send Approval Email
      return;
    }

    Log::Info("Sending emails for approval stage");
    // Send emails
  }


}
