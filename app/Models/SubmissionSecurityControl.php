<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionSecurityControl extends Model
{
    use HasFactory;

    protected $fillable = [
      "submission_id",
      "sra_submission_id",
      "security_catalogue_name",
      "name",
      "risk_weights",
      "description",
      "implementation_guidance",
      "implementation_evidence",
      "audit_guidance",
      "reference_standards",
      "control_owner_name",
      "control_owner_email",
      "tags"      
    ];

    public function populate($controlId) {
      $riskWeights = SecurityControlRiskWeight::with("risk")->where(["security_control_id" => $controlId])->get();

      $additionalValues = array();
      $output = array();
      foreach($riskWeights as $riskWeight) {
        $riskName = $riskWeight->risk->name;
        $output[$riskName] = array(
          "likelihood_weight" => $riskWeight->likelihood,
          "likelihood_penalty" => $riskWeight->likelihood_penalty,
          "impact_weight" => $riskWeight->impact,
          "impact_penalty" => $riskWeight->impact_penalty
        );    
      } // foreach($riskWeights as $riskWeight)

      $this->risk_weights = json_encode($output);
    }
}
