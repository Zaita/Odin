<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',    
    'type',
    'sort_order'
  ];

  protected $hidden = [
    "task_object_id",
    "created_at",
    "updated_at"
  ];

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
