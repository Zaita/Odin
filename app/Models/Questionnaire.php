<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\QuestionnaireQuestion;
use App\Models\InputField;
use App\Models\ActionField;

class Questionnaire extends Model
{
    use HasFactory;

    protected $table = 'questionnaires';
    
    protected $fillable = [
      'name',
      'type',
    ];

    protected $hidden = [
      "created_at",
      "updated_at"
    ];
  
    public function questions(): HasMany {
      return $this->hasMany(QuestionnaireQuestion::class);
    }

    /**
     * Convert a JSON input string into a questionnaire
     */
    public function importFromJson($jsonInput) {
      $this->name = $jsonInput->name;
      $this->type = $jsonInput->type;
      $this->save();
      printf("New Questionnaire created '%s' with id: %d\n", $this->name, $this->id);
      
      $questionSortOrder = 0;
      foreach($jsonInput->questions as $question) {
        $q = new QuestionnaireQuestion();
        $q->questionnaire_id = $this->id;
        $q->title = $question->title;
        $q->heading = $question->heading;
        $q->description = $question->description;
        $q->sort_order = $questionSortOrder++;
        $q->save();

        printf("New Question: %s created with id %d\n", $q->title, $q->id);
        if (isset($question->answerInputFields)) {
          foreach($question->answerInputFields as $inputField) {
            $f = new InputField();
            $f->questionnaire_question_id = $q->id;
            $f->label = $inputField->label;
            $f->input_type = $inputField->inputType;
            $f->required = $inputField->required;
            $f->min_length = $inputField->minLength;
            $f->max_length = $inputField->maxLength;
            $f->placeholder = $inputField->placeHolder;
            $f->product_name = $inputField->productName;
            $f->business_owner = $inputField->businessOwner;
            $f->release_date = isset($inputField->releaseDate) ? isset($inputField->releaseDate) : false;
            $f->ticket_url = $inputField->ticketUrl;
            $f->save();
            printf("New Input Field: %s with id %d\n", $f->label, $f->id);
          }
        }

        if (isset($question->answerActionFields)) {
          foreach($question->answerActionFields as $actionField) {
            $f = new ActionField();
            $f->questionnaire_question_id = $q->id;
            $f->label = $actionField->label;
            $f->action_type = $actionField->actionType;
            $f->goto_question_title = isset($actionField->gotoQuestionTitle) ? $actionField->gotoQuestionTitle : null;
            $f->tasks = isset($actionField->tasks) ? json_encode($actionField->tasks) : null;
            $f->save();
            printf("New Action Field: %s with id %d\n", $f->label, $f->id);
          }
        }
      }
    }

}
