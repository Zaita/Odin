<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Objects\RiskCalculatorObject;

$uuid = Str::uuid()->toString();

/**
 * Task Submission:
 * 
 * Possible states:
 * - ready_to_start
 * - in_progress
 * - complete
 * - waiting_for_approval
 * - approved
 * - denied
 * - not_applicable
 */
class TaskSubmission extends Model
{
    use HasFactory;

    protected $table = 'task_submissions';

    protected $fillable = [
        'status',
        'uuid',
        'submitter_name',
        'submitter_email',
        'questionnaire_data',
        'answer_data',
        'approver_name',
        'result'
    ];

    protected $appends = [
      'time_to_complete',
      'time_to_review',
      'approved_by'
    ];

    protected $hidden = [
      'submission_id',
      'approver_id',
    ];

    /**
     * Construct our submission with some default values
     */
  public function __construct(array $attributes = array()) {
    parent::__construct($attributes);
    $this->uuid = Str::uuid()->toString();
    $this->status = "ready_to_start";
    $this->answer_data = null;
  }

  /**
   * Bind simple attribute for Model to use.
   */
  protected function TimeToComplete() : Attribute {
    $x = function() {
      return json_decode($this->task_data)->name;
    };
    return Attribute::make(
      get: fn (null $value) => "-",
    );
  }

  /**
   * Bind simple attribute for Model to use.
   */
  protected function TimeToReview() : Attribute {
    $x = function() {
      return json_decode($this->task_data)->name;
    };
    return Attribute::make(
      get: fn (null $value) => "-",
    );
  }  

  /**
   * Bind simple attribute for Model to use.
   */
  protected function ApprovedBy() : Attribute {
    $x = function() {
      return json_decode($this->task_data)->name;
    };
    return Attribute::make(
      get: fn (null $value) => "-",
    );
  }   

  /**
   * Override the save here so we can populate some values that haven't been populated yet
   */
  public function save(array $options = []) {
    // This is likely first save, we need to populate the default
    // answer data if this is a questionnaire type
    if ($this->answer_data == null) {
      if ($this->task_type == "questionnaire" || $this->task_type == "risk_questionnaire") {
        $questions = json_decode($this->task_data)->questionnaire->questions;

        $answers = [];
        foreach($questions as $question) {
          $newAnswer = array();
          $newAnswer["question"] = $question->title;
          $newAnswer["status"] = "incomplete";
          $newAnswer["data"] = array();
          array_push($answers, $newAnswer);     
        }
    
        $answerData = array("answers" => $answers);
    
        $this->answer_data = json_encode($answerData);
      }

      // Hack
      if ($this->answer_data == null) {
        $this->answer_data = "{}";
      }
    }
    parent::save($options);
  }

  public function calculateRiskScore() {
    Log::Info("Calculating Risk");
    Log::Info(sprintf("Type: %s; Calculation: %s", $this->task_type, $this->risk_calculation));
    /**
     * Step 1: Calculate the risk values
     */    
    if ($this->task_type == "risk_questionnaire" && $this->risk_calculation != "none") {
      Log::Info("Risks are required");
      $this->risk_data = RiskCalculatorObject::calculate($this, $this->risk_calculation);
      $this->save();      
    }
  }

  /**
   * Create a task submission on a submission
   */
  public static function Create(string $taskName, Submission $submission, bool $autoApprove) {
    $taskObj = Task::where(["name" => $taskName])->first();
        Log::Info("Adding task $taskName to submission $submission->uuid");

        if ($taskObj->type == "questionnaire" || $taskObj->type == "risk_questionnaire") {
          $questionnaire = Questionnaire::with([
            "questions" => function(Builder $q) {$q->orderBy('sort_order');},
            "questions.inputFields" => function(Builder $q) {$q->orderBy('sort_order');},
            "questions.inputFields.input_options" => function(Builder $q) {$q->orderBy('sort_order');},
            "questions.actionFields" => function(Builder $q) {$q->orderBy('sort_order');},
            ])->findOrFail($taskObj->task_object_id);

          $taskObj->questionnaire = $questionnaire;

          // Use first or new so we don't duplicate tasks here. Only 1 instance of each task per submission
          $taskSubmission = TaskSubmission::firstOrNew(["name" => $taskObj->name, "submission_id" => $submission->id]);
          $taskSubmission->name = $taskObj->name;
          $taskSubmission->submission_id = $submission->id;
          $taskSubmission->task_type = $taskObj->type;
          $taskSubmission->task_data = $taskObj;
          $taskSubmission->risk_data = "{}";
          if ($autoApprove) {
            $taskSubmission->status = 'not_applicable';
          }

          if ($taskObj->questionnaire->custom_risks) {
            $risks = QuestionnaireRisk::where(['questionnaire_id' => $taskObj->questionnaire->id])->get();
            $riskNames = array();
            foreach($risks as $risk) {
              array_push($riskNames, ["name" => $risk->name, "description" => ""]);
            }
            $taskSubmission->risks = json_encode($riskNames);
          } else {
            $risks = Risk::All();
            $riskNames = array();
            foreach($risks as $risk) {
              array_push($riskNames, ["name" => $risk->name, "description" => $risk->description]);
            }
            $taskSubmission->risks = json_encode($riskNames);

          $taskSubmission->save();
          }
        } else if ($taskObj->type == "security_risk_assessment") {
          // Create the task submission object for the DSRA
          $taskSubmission = TaskSubmission::firstOrNew(["name" => $taskObj->name, "submission_id" => $submission->id]);
          $taskSubmission->name = $taskObj->name;
          $taskSubmission->submission_id = $submission->id;
          $taskSubmission->task_type = $taskObj->type;
          $taskSubmission->task_data = $taskObj;
          $taskSubmission->risk_data = "{}";
          $taskSubmission->risks = "{}";
          if ($autoApprove) {
            $taskSubmission->status = 'not_applicable';
          }          
          $taskSubmission->save();
          // Create the DSRA task submission information
          $sra = SecurityRiskAssessment::with(
            "security_catalogue",
            "security_catalogue.security_controls", 
            "initial_risk_impact")->findOrFail($taskObj->task_object_id);
          $sraSubmission = SecurityRiskAssessmentSubmission::firstOrNew(["task_submission_id" => $taskSubmission->id]);
          $sraSubmission->populate($taskSubmission, $sra);

          // Create our security control submission information
          $catalogueName = $sra->security_catalogue->name;
          Log::Info("Security catalogue $catalogueName will be assigned to this submission");
          foreach($sra->security_catalogue->security_controls as $control) {
            $dbControl = SubmissionSecurityControl::firstOrNew(["submission_id" => $submission->id, 
              "sra_submission_id" => $sraSubmission->id,
              "name" => $control->name]);
            $dbControl->security_catalogue_name = $catalogueName;
            $dbControl->fill(json_decode($control, true));
            $dbControl->populate($control->id);
            $dbControl->save();
          }
        }  
  }
}

