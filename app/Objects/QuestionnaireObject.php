<?php
namespace App\Objects;

use Illuminate\Support\Facades\Log;

/**
 * This object is an instance of a questionnaire or risk_questionnaire.
 * We use this object interchangeably between submissions and tasks so we don't
 * need to duplicate a whole heap of code.
 * 
 * This is because the type of a task can be a questionnaire or risk_questionnaire. 
 * These are the type of objects usable by a submission
 */
class QuestionnaireObject {
  protected $questions = null;
  public $answers = null;
  protected $lastQuestion = '';
  public $errors = array();

  /**
   * Construct our questionnaire object
   */
  function __construct(String $questions, String $answers, $fromTask=false) {
    $this->questions = json_decode($questions);
    $this->answers = json_decode($answers);

    if ($fromTask) {
      $this->questions = $this->questions->questionnaire->questions;
    }
  }

  /**
   * Validate Business logic in our questionnaire text
   */
  public function validateAnswers($currentQuestionTitle, $newAnswerValues) : bool {
    Log::Info("QuestionnaireObject.validateAnswers(currentQuestionTitle: ${currentQuestionTitle})");
    $errors = array();

    // Find the target question in our questionnaire
    $targetQuestion = null;
    foreach($this->questions as $question) {
      if ($question->title == $currentQuestionTitle) {
        $targetQuestion = $question;
        break;
      }
    }

// Check if we can skip validation. This is because there
    // are no defined Answer Input Fields on this question
    if (!isset($targetQuestion->input_fields)) {
      return true; // No validation required.
    }

    // Loop over each answerInputField in our questionnaire
    foreach($targetQuestion->input_fields as $inputField) { 
      if ($inputField->input_type == "checkbox") {
        continue; // no validation to do on checkboxs
      }

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

    $this->errors = $errors;
    return count($errors) == 0;
  }
  
  /**
   * Process the user answers. This means we'll need to update the answer data to be NotApplicable if we're doing
   * things like gotos
   */
  public function processAnswers($currentQuestionTitle, $newAnswers, $actionValue) : bool {
    $this->setAnswers($currentQuestionTitle, $newAnswers);

    if ($actionValue != null) {
      foreach($this->questions as $question) {
        if ($question->title == $currentQuestionTitle) {
          foreach($question->action_fields as $actionField) {
            if ($actionField->label == $actionValue) {
              if ($actionField->action_type != "goto") {
                break 2; // Stop both loops
              }
              // We have a go to
              $this->handleGoto($currentQuestionTitle, $actionField->goto_question);
              $this->lastQuestion =  $actionField->goto_question;
            }
          }
          break;          
        }
      }
    }

    $this->answers->last_question = $this->lastQuestion == null ? $currentQuestionTitle : $this->lastQuestion;
    return count($this->errors) == 0;
  }


    /**
   * Set the answer on a specific question with the target field
   */
  public function setAnswers($question, $newAnswers) {
    Log::Info("questionnaireobject.setAnswer($question)");

    $elementsToAdd = array();
    foreach($this->answers->answers as $answerEntry) {
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
  public function handleGoto($currentQuestion, $targetQuestion) {
    $startMarking = false;
    foreach($this->answerData->answers as $answerEntry) {
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

  public function isLastQuestion($currentQuestion) {
    $index = null;
    for ($x = 0; $x < count($this->questions); $x++) {
      if ($this->questions[$x]->title == $currentQuestion) {
        $index = $x + 1;
        break;
      }
    }

    if ($index != null && $index == count($this->questions)) {
      Log::Info("isLastQuestion: True");
      return true;
    }

    Log::Info("isLastQuestion: False");
    return false;
  }
}