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
    'lock_when_complete',
    'approval_required',
    'risk_calculation',
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

    $q = Questionnaire::firstOrNew(["name" => $this->name]);
    $q->importFromJson($jsonArr);
    $this->task_object_id = $q->id;    

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
    /**
     * If the task does not have a task_object_id then this is the first
     * save and we need to create the child object this task will point to.
     * 
     * Depending on the task type, this will be handled differently.
     */
    if (is_null($this->task_object_id)) {
      if ($this->type == "questionnaire") {
        $q = new Questionnaire();
        $q->name = $this->name;
        $q->type = "questionnaire";
        $q->save();
        $this->task_object_id = $q->id;
      }
    }

    return parent::save($options);
  }
}
