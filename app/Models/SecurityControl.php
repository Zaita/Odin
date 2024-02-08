<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\SecurityControlRiskWeight;
class SecurityControl extends Model
{
    use HasFactory;
    protected $fillable = [
        "name", 
        "description", 
        "implementation_guidance", 
        "implementation_evidence", 
        "audit_guidance", 
        "reference_standards", 
        "control_owner_name",
        "control_owner_email", 
        "tags",
     ];

    public function risk_weights(): HasMany {
      return $this->hasMany(SecurityControlRiskWeight::class);
    }

    public $errors = array();
    /**
    * 
    */
    public function updateRisks(array $inputs) {
      Log::Info("Updating Risks For Security Control ($this->id)");
      $riskBlock = array();
      // Validate the Risks
      foreach ($inputs as $key => $value) {
        if (!str_contains($key, "||")) {
          continue; // Not a risk field
        }
        Log::Info("Key $key");
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
          if ($value < 0 || $value > 10) {
            $this->errors[$key] = "Impact value must be between 0 and 10 inclusively";
          }
          $riskBlock[$risk]["impact"] = (int)$value;

        } else if ($modifier == "impact_penalty") {
          if ($value < 0) {
            $this->errors[$key] = "Impact penalty must be greater than 0";
          }
          $riskBlock[$risk]["impact_penalty"] = (int)$value;
        }
      } // foreach ($inputs as $key => $value)

      if (count($this->errors) > 0) {
        Log::Info("Errors Detected");
        return;
      }

      $riskCount = count($riskBlock);
      Log::Info("Parsed $riskCount risks");

      // Now we create each risk weight we received
      foreach ($riskBlock as $riskName => $risk) {
        if (!isset($risk["likelihood"])) {
          continue;
        }
        if (!isset($risk["likelihood_penalty"])) {
          continue;
        }
        if (!isset($risk["impact"])) {
          continue;
        }
        if (!isset($risk["impact_penalty"])) {
          continue;
        }        
        $likelihood = (int)$risk["likelihood"];
        $likelihood_penalty = (int)$risk["likelihood_penalty"];
        $impact = (int)$risk["impact"];
        $impact_penalty = (int)$risk["impact_penalty"];
        if ($likelihood + $likelihood_penalty + $impact + $impact_penalty == 0) {
          Log::Info("Skipping $riskName because it had no weights");
          continue; // Skip this risk as we have no weights
        }
        Log::Info("Adding weights for risk '$riskName'");

        $riskId = Risk::where(["name" => $riskName])->first()->id;

        $riskWeight = SecurityControlRiskWeight::firstOrNew(["security_control_id" => $this->id, "risk_id" => $riskId]);
        $riskWeight->likelihood = $likelihood;
        $riskWeight->likelihood_penalty = $likelihood_penalty;
        $riskWeight->impact = $impact;
        $riskWeight->impact_penalty = $impact_penalty;
        $riskWeight->risk_id = $riskId;
        $riskWeight->security_control_id = $this->id;
        $riskWeight->save();
      }
    }
}

