<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Risk;

class InputOption extends Model
{
    use HasFactory;

  public $errors = array();

    protected $fillable = [
      'label',
      'value',
      'risks',
    ];

    protected $casts = [
      'risks' => 'json',
    ];

    public function validateAnswers(array $answers) {
      Log::Info("Validating Answers");
      foreach ($answers as $key => $value) {
        Log::Info(sprintf("%s == %s", $key, $value));
      }

      // Validate Label
      if (!array_key_exists("label", $answers) || $answers["label"] == "") {
        $this->errors["label"] = "Label is a required field";
      } else if (strlen($answers["label"]) >= 128) {
        $this->errors["label"] = "Label max length is 128";
      }

      // Validate value
      if (!array_key_exists("value", $answers) || $answers["value"] == "") {
        $this->errors["value"] = "Value is a required field";
      } else if (strlen($answers["label"]) >= 128) {
        $this->errors["value"] = "Value max length is 128";
      }

      $this->label = $answers["label"];
      $this->value = $answers["value"];

      if ($this->risks == null) {
        $this->risks = "{}";
      }
      
      $riskBlock = array();
      // Validate the Risks
      foreach ($answers as $key => $value) {
        if (!str_contains($key, "||")) {
          continue; // Not a risk field
        }

        $keys = explode("||", $key);
        $risk = $keys[0];
        $modifier = $keys[1];

        if (!isset($riskBlock[$risk])) {
          $riskBlock[$risk] = array();
        }

        if ($modifier == "likelihood") {
          if ($value < 0 || $value > 10) {
            $this->errors[$key] = "Likelihood value must be between 0 and 10 inclusively.";
          }
          $riskBlock[$risk]["likelihood"] = (int)$value;

        } else if ($modifier == "likelihood_penalty") {
          if ($value < 0) {
            $this->errors[$key] = "Likelihood penalty must be greater than 0";
          }
          $riskBlock[$risk]["likelihood_penalty"] = (int)$value;


        } else if ($modifier == "impact") {
          // if ($value < 0 || $value > 10) {
          //   $this->errors[$key] = "Impact value must be between 0 and 10 inclusively";
          // }
          $riskBlock[$risk]["impact"] = (int)$value;

        } else if ($modifier == "impact_penalty") {
          if ($value < 0) {
            $this->errors[$key] = "Impact penalty must be greater than 0";
          }
          $riskBlock[$risk]["impact_penalty"] = (int)$value;
        }
      }

      if (count($this->errors) > 0) {
        return;
      }

      $this->risks = $riskBlock;

      $this->Save();
      Log::Info("Done");      
    }
}
