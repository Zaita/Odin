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
