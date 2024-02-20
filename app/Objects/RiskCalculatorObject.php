<?php
namespace App\Objects;

use Illuminate\Support\Facades\Log;

use App\Models\Risk;
use App\Models\ImpactThreshold;

/**
 * This object is responsible for calculating the risk_data for a submission
 * based on the associated risk calculation methodology
 */
class RiskCalculatorObject {

  public static function calculate($submission, $method) {
    $risks = RiskCalculatorObject::highest_value($submission);
    $risks = RiskCalculatorObject::getImpactRating($risks);
    return $risks;
  }

  public static function getImpactRating($risks) {
    $thresholds = ImpactThreshold::orderBy("sort_order", "asc")->get();

    foreach($risks as &$risk) {
      foreach($thresholds as $threshold) {
        if ($threshold->operator == "<" && $risk["score"] < $threshold->value) {
          $risk["color"] = $threshold->color;
          $risk["rating"] = $threshold->name;
          break;
        
        } else if ($threshold->operator == ">=" && $risk["score"] >= $threshold->value) {
          $risk["color"] = $threshold->color;
          $risk["rating"] = $threshold->name;
          break;
        }
      }
    }

    return json_encode($risks);
  }

  public static function highest_value($submission) {
    Log::Info("Calculating Risks with highest_value algorithm");
    $risks = Risk::all(); // Todo: support Custom Risks here

    $riskScores = array();
    foreach ($risks as $risk) {
      $riskScores[$risk->name] = 0;
    }

    // Loop through the questionnaire looking for checkbox input types
    $questions = json_decode($submission->questionnaire_data);
    $answers = json_decode($submission->answer_data);
    foreach ($questions as $question) {
      foreach($question->input_fields as $inputField) {
        if ($inputField->input_type != "checkbox" && $inputField->input_type != "radio") {
          continue; // We only allow risks on checkbox and radio types
        }
        foreach($inputField->input_options as $inputOption) {
          Log::Info("Checking Input: $inputOption->label");
          if (isset($inputOption->risks)) {
            Log::Info("Found checkbox option $inputOption->label with risks");
            // We have risks now, we want to load the answer data
            foreach($answers->answers as $answer) {
              if ($answer->question == $question->title) { // match the question to an answer
                Log::Info("Found answer in question $question->title");
                // each answer field has data which contains each field                
                foreach($answer->data as $field) {
                  if (is_object($field->value)) { // Checkboxes
                    Log::Info("Found array with $field->field");
                    foreach($field->value as $checkboxLabel => $checkBoxAnswer) {
                      Log::Info("CheckboxAnswer for $checkboxLabel is $checkBoxAnswer");
                      if ($checkBoxAnswer && $checkboxLabel == $inputOption->label) { // Did the user mark this as true
                        Log::Info("User selected checkbox option $checkboxLabel");
                        // Loop thr risks on the checkbox option
                        foreach ($inputOption->risks as $riskName => $riskData) {
                          if (isset($riskData->impact)) {
                            $riskScores[$riskName] = max($riskScores[$riskName], $riskData->impact);
                          }
                        }
                      }
                    }
                  } else if (is_string($field->value)) { // Radio Buttons
                    if ($field->value == $inputOption->label) {
                      Log::Info("User selected radio option $inputOption->label");
                      // Loop thr risks on the radio option
                      foreach ($inputOption->risks as $riskName => $riskData) {
                        if (isset($riskData->impact)) {
                          $riskScores[$riskName] = max($riskScores[$riskName], $riskData->impact);
                        }
                      }                      
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    $riskData = array();
    foreach($risks as $risk) {
      if (isset($riskScores[$risk->name]) && $riskScores[$risk->name] > 0) {
        $newRisk = array();
        $newRisk["name"] = $risk->name;
        $newRisk["score"] = $riskScores[$risk->name];
        $newRisk["description"] = $risk->description;
        array_push($riskData, $newRisk);
      }
    }
    
    return $riskData;
  }
}