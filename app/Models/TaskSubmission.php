<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
}

