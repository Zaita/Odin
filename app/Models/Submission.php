<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\QuestionnaireQuestion;

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
      'created_at_short'
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
   * Get the status of all the tasks for this submission so 
   * we can report it on the submissions and summary pages.
   */
  protected function getTasksStatuses() { 
    if ($this->status == "in_progress") {
      return "n/a";
    }

    return "0/0";
  }

  /**
   * Bind simple attribute for Model to use.
   */
  protected function TasksCompleted() : Attribute {
    return Attribute::make(
      get: fn (null $value) => $this->getTasksStatuses(),
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
    // foreach($questionData as $question) {
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

      $value = $newAnswerValues[$label];
      if (isset($inputField->required) && $inputField->required && ($value == null || $value == "")) {
        $errors[$label] = "$label is a required field";
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
}
