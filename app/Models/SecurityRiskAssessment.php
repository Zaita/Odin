<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityRiskAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'key_information',
      'custom_likelihoods',
      'custom_impacts',
      'likelihood_thresholds',
      'impact_thresholds',
      'risk_matrix'
    ];

    protected $casts = [
      'likelihood_thresholds' => 'json',
      'impact_thresholds' => 'json',
      'risk_matrix' => 'json',
    ];

    public function security_catalogue(): BelongsTo {
      return $this->BelongsTo(SecurityCatalogue::class);
    }
  
    public function initial_risk_impact(): BelongsTo {
      return $this->BelongsTo(Task::class);
    }

    public function importFromJson($jsonArr) { 
      Log::Info("SecurityRiskAssessment.importFromJson()");
      // Strip our the extra JSON fields not related to this object
      $relevantJson = array_filter($jsonArr, function($k) { 
        return in_array($k, $this->fillable);
      }, ARRAY_FILTER_USE_KEY);
  
      $this->fill($relevantJson);  
      $this->save();
    }


    public function save(array $options = []) { 
      if ($this->risk_matrix == null) {
        $this->risk_matrix = $this->buildDefaultRiskMatrix();
      }

      return parent::save($options);
    }


    /**
     * Build a default risk matrix so that we can make the configuration 
     * much much easier
     */
    protected function buildDefaultRiskMatrix() {      
      $likelihoods = LikelihoodThreshold::orderBy('sort_order', 'asc')->get();
      $impacts = ImpactThreshold::orderBy('sort_order', 'asc')->get();

      $getLikelihoodId = function($name) use ($likelihoods) {
        return $name;
      };

      $getImpactId = function($name) use ($likelihoods) {
        return $name;
      };

      // Green = #00AB00 Yellow = #FFFF00 Orange = #FF8400 Red = FA0000
      // $likelihoodRatings = ["Rare", "Unlikely", "Possible", "Likely", "Almost Certain"];
      // $impactRatings = ["Insignificant", "Minor", "Moderate", "Severe", "Extreme"];
      // $riskRatings = array(["Low", "Medium", "High", "Critical"]);
      // $ratingColors = array(["Low" => "green", "Medium" => "yellow", "High" => "red", "Critical" => "darkRed"]);
      $riskMatrix = array();
      if (!$this->custom_likelihoods && !$this->custom_impacts) {
        
        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Rare"), "impact" => $getImpactId("Insignificant"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Rare"), "impact" => $getImpactId("Minor"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Rare"), "impact" => $getImpactId("Moderate"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Rare"), "impact" => $getImpactId("Severe"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Rare"), "impact" => $getImpactId("Extreme"), "color" => "#FF8400"];

        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Unlikely"), "impact" => $getImpactId("Insignificant"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Unlikely"), "impact" => $getImpactId("Minor"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Unlikely"), "impact" => $getImpactId("Moderate"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Unlikely"), "impact" => $getImpactId("Severe"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Unlikely"), "impact" => $getImpactId("Extreme"), "color" => "#FF8400"];

        $riskMatrix[] = ["rating" => "Low", "likelihood" => $getLikelihoodId("Possible"), "impact" => $getImpactId("Insignificant"), "color" => "#00AB00"];
        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Possible"), "impact" => $getImpactId("Minor"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Possible"), "impact" => $getImpactId("Moderate"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Possible"), "impact" => $getImpactId("Severe"), "color" => "#FF8400"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Possible"), "impact" => $getImpactId("Extreme"), "color" => "#FA0000"];

        $riskMatrix[] = ["rating" => "Medium", "likelihood" => $getLikelihoodId("Likely"), "impact" => $getImpactId("Insignificant"), "color" => "#FFFF00"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Likely"), "impact" => $getImpactId("Minor"), "color" => "#FF8400"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Likely"), "impact" => $getImpactId("Moderate"), "color" => "#FF8400"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Likely"), "impact" => $getImpactId("Severe"), "color" => "#FA0000"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Likely"), "impact" => $getImpactId("Extreme"), "color" => "#FA0000"];

        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Almost Certain"), "impact" => $getImpactId("Insignificant"), "color" => "#FF8400"];
        $riskMatrix[] = ["rating" => "High", "likelihood" => $getLikelihoodId("Almost Certain"), "impact" => $getImpactId("Minor"), "color" => "#FF8400"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Almost Certain"), "impact" => $getImpactId("Moderate"), "color" => "#FA0000"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Almost Certain"), "impact" => $getImpactId("Severe"), "color" => "#FA0000"];
        $riskMatrix[] = ["rating" => "Critical", "likelihood" => $getLikelihoodId("Almost Certain"), "impact" => $getImpactId("Extreme"), "color" => "#FA0000"];        
    }
    return json_encode($riskMatrix);
  }


  public function update_initial_risk_impact_task($taskName) {
    $this->initial_risk_impact_id = Task::where(["name" => $taskName])->first()->id;
  }
}
