<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\TaskSubmission;

$uuid = Str::uuid()->toString();

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

    // protected $casts = [
    //   'created_at' => 'datetime:Y-m-d',
    //   'release_date' => 'datetime:Y-m-d'
    // ];

    /**
     * Construct our submission with some default values
     */
  public function __construct(array $attributes = array()) {
    parent::__construct($attributes);
    $this->uuid = Str::uuid()->toString();
    $this->status = "in_progress";
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
      // if (isset($inputField->business_owner) && $inputField->business_owner) {
        // $this->business_owner = $value;
      // }      
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
    if ($this->task_count() != $this->completed_task_count()) {
      $this->errors["submit"] = "Not all tasks have been completed";
      return false;
    }

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
    }

    return "-";
  }

  protected function task_count() {
    $taskList = TaskSubmission::where(["submission_id" => $this->id])->get();
    return count($taskList);
  }

  protected function completed_task_count() {
    $taskList = TaskSubmission::where(["submission_id" => $this->id,])
      ->whereIn('status', ['complete', 'approved'])->get();
    return count($taskList);
  }
}
