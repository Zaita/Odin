<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Task
 */
class Task extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',    
    'type',
    'key_information',
    'show_information_screen',
    'lock_when_complete',
    'approval_required',
    'approval_group',
    'notification_group',
    'sort_order',
  ];

  protected $hidden = [
    "sort_order",
    "task_object_id",
    "created_at",
    "updated_at"
  ];

  public function questionnaire(): BelongsTo {
    return $this->belongsTo(Questionnaire::class);
  }

  /**
   * Import a task from JSON
   */
  public function importFromJson($jsonArr) { 
    Log::Info("task.importFromJson()");
    // Strip our the extra JSON fields not related to this object
    $relevantJson = array_filter($jsonArr, function($k) { 
      return in_array($k, $this->fillable);
    }, ARRAY_FILTER_USE_KEY);

    $this->fill($relevantJson);  

    if ($this->type == "questionnaire" || $this->type == "risk_questionnaire") {
      $q = Questionnaire::firstOrNew(["name" => $this->name]);
      if (isset($jsonArr["questionnaire"])) {
        $q->importFromJson($jsonArr["questionnaire"]);
      } else {
        $q->importFromJson($jsonArr);
      }
      $this->task_object_id = $q->id;    

    } else if ($this->type == "security_risk_assessment") {
      $dsra = SecurityRiskAssessment::firstOrNew(["name" => $this->name]);
      $dsra->importFromJson($jsonArr);
      $this->task_object_id = $dsra->id;
    } else {
      $this->task_object_id = 0;
    }

    $this->save();
  }

  /**
   * If we've loaded a new Task object then it's likely during the import of JSON
   * where an action field is linking to a task. In this scenario we want
   * to create a new questionnaire and link it to this task with no questions.
   */
  public function defaultSetupIfNew() {
    // Task already setup
    if (!is_null($this->task_object_id))
      return;

    $q = new Questionnaire();
    $q->name = $this->name;
    $q->type = "questionnaire";
    $q->save();
    $this->task_object_id = $q->id;

    $this->save();    
  } 

  /**
   * Override the save for the Task. 
   */
  public function save(array $options = []) { 
    Log::Info("Task.save($this->name)");
    /**
     * If the task does not have a task_object_id then this is the first
     * save and we need to create the child object this task will point to.
     * 
     * Depending on the task type, this will be handled differently.
     */
    if (is_null($this->task_object_id)) {
      Log::Info("First save of task, assigning a task_object_id");
      // Create default questionnaire or risk questionnaire for a new task
      if ($this->type == "questionnaire" || $this->type == "risk_questionnaire") {
        $q = new Questionnaire();
        $q->name = $this->name;
        $q->type = $this->type;
        $q->save();
        $this->task_object_id = $q->id;
      
      } else if ($this->type == "security_risk_assessment") {
        Log::Info("Creating new Security Risk Assessment child for task");
        // Create default fields for DSRA
        $dsra = SecurityRiskAssessment::firstOrNew(["name" => $this->name]);
        $dsra->name = $this->name;
        $dsra->security_catalogue_id = SecurityCatalogue::first()->id;
        $dsra->save();
        $this->task_object_id = $dsra->id;

      } else {
        $this->task_object_id = 0;
      }
    }

    if ($this->approval_group == "none") {
      $this->approval_group = null;
    }
    if ($this->notification_group == "none") {
      $this->notification_group = null;
    }

    return parent::save($options);
  }
}
