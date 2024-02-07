<?php
namespace App\Objects;

use Illuminate\Support\Facades\Log;

/**
 * This object is responsible for calculating the risk_data for a submission
 * based on the associated risk calculation methodology
 */
class RiskCalculatorObject {

  public static function calculate($submission, $method) {
    return RiskCalculatorObject::highest_value($submission);
  }

  public static function highest_value($submission) {
    $risks = array();

    $riskScores = array();

    $questions = json_decode($submission->questionnaire_data);
    foreach ($questions as $question) {
      foreach($question->input_fields as $inputField) {
        foreach($inputField->checkbox_options as $checkboxOption) {
          if (isset($checkboxOption->risks)) {
            $risks = json_decode($checkboxOption->risks);

            foreach($risks as $riskName => $riskValue) {
              Log::debug("A". $riskName);
              if (isset($riskValue->likelihood))
                Log::debug($riskValue->likelihood);
            }
          }
        }
      }
    }

    $answers = json_decode($submission->answer_data);
    foreach ($answers->answers as $answer) { 
    }

    $risk = array();
    $risk["name"] = "Information SSLoss";
    $risk["description"] = "Blah blah bblah";
    $risk["score"] = "100";
    array_push($risks, $risk);
    
    return json_encode($risks);
  }

}