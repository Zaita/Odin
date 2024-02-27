<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SecurityRiskAssessmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
      'task_submission_id',
      'require_validation_audit',
      'likelihood_thresholds',
      'impact_thresholds',
      'risk_matrix'
    ];


    public function populate(TaskSubmission $task, SecurityRiskAssessment $sra) {
      $this->task_submission_id = $task->id;

      // We need to populate the initial_risk_impact_id value with the TaskSubmission->id matching
      // the task we expect to have been created.
      Log::Info("Searching for initial_risk_impact task: $sra->initial_risk_impact_id");
      $targetTask = Task::where('id', '=', $sra->initial_risk_impact_id)->first();
      $targetTaskName = $targetTask->name;

      $taskSubmission = TaskSubmission::where(['name' => $targetTaskName, 'submission_id' => $task->submission_id])->first();
      $this->initial_risk_id = $taskSubmission->id;

      $this->require_validation_audit = $sra->require_validation_audit;
      $this->likelihood_thresholds = $sra->custom_likelihoods ? $sra->likelihood_thresholds : LikelihoodThreshold::all_as_json_str();
      $this->impact_thresholds = $sra->custom_impacts ? $sra->impact_thresholds : ImpactThreshold::all_as_json_str();
      $this->risk_matrix = json_encode($sra->risk_matrix);
      $this->save();
    }
}
