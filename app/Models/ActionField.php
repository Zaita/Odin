<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ActionField extends Model
{
    use HasFactory;

    protected $table = 'action_fields';
    
    protected $fillable = [
      'question_id',
      'label',
      'action_type',
      'goto_question_title',
      'tasks',       
    ];

    protected $hidden = [
      "questionnaire_question_id",
      "created_at",
      "updated_at",
    ];

    public $errors = array();

  /**
   * Import An InputField from JSON
   */
  public function importFromJson($jsonArr, $questionId) {
    Log::Info("ActionField.importFromJson()");

    // Strip our the extra JSON fields not related to this object
    $relevantJson = array_filter($jsonArr, function($k) { 
      return in_array($k, $this->fillable);
    }, ARRAY_FILTER_USE_KEY);

    $this->fill($relevantJson);  
    $this->questionnaire_question_id = $questionId;
    $this->save(); // generate $this->id

    /**
     * Check if the associated tasks exists or not.
     * If they don't exist, we'll create a blank questionnaire
     * as a placeholder
     */
    if (!is_null($this->tasks)) {
      $tasks = json_decode($this->tasks);
      foreach($tasks as $task) {
        $t = Task::firstOrNew(["name" => $task->name]);
        $t->defaultSetupIfNew($task->name);        
      }
    }
  }

  /**
   * Check if this input field is a valid candidate for being added
   * to the question parameter
   * 
   * @param question that the input field will be added to
   * @return true on success, false otherwise
   */
  public function isValid($question) {
    if (!isset($question->actionFields)) {
      Log::Info("Question has no action fields");
      return true;
    }

    Log::Info("Checking if ActionField.isValid()"); 

    if (count($this->errors) > 0) {
      return false;
    }

    return true;
  }
}
