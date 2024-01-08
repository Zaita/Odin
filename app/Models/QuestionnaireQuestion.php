<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

use App\Models\InputField;
use App\Models\ActionField;

class QuestionnaireQuestion extends Model
{
  use HasFactory;

  protected $table = 'questionnaire_questions';

  protected $fillable = [
    'title',
    'heading',
    'description',
    'sort_order',
  ];

  protected $hidden = [
    "questionnaire_id",
    "created_at",
    "updated_at"
  ];

  public function inputFields(): HasMany {
    return $this->hasMany(InputField::class);
  }

  public function actionFields(): HasMany {
    return $this->hasMany(ActionField::class);
  }

  /**
   * Import our Questions from JSON
   */
  public function importFromJson($jsonArr, $questionnaireId) {
    Log::Info("QuestionnaireQuestion.importFromJson()");

    // Strip our the extra JSON fields not related to this object
    $relevantJson = array_filter($jsonArr, function($k) { 
      return in_array($k, $this->fillable);
    }, ARRAY_FILTER_USE_KEY);

    $this->fill($relevantJson);  
    $this->questionnaire_id = $questionnaireId;
    $this->save();

    if (array_key_exists("input_fields", $jsonArr)) {
      foreach($jsonArr["input_fields"] as $inputField) {
        $f = InputField::firstOrNew([
          "questionnaire_question_id" => $this->id,
          "label" => $inputField["label"]
        ]);
        $f->importFromJson($inputField, $this->id);
      }
    }

    if (array_key_exists("action_fields", $jsonArr)) {
      foreach($jsonArr["action_fields"] as $actionField) {
        $f = ActionField::firstOrNew([
          "questionnaire_question_id" => $this->id,
          "label" => $actionField["label"]
        ]);
        $f->importFromJson($actionField, $this->id);
      }
    }
  }
}
