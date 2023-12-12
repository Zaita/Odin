<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\InputField;
use App\Models\ActionField;

class QuestionnaireQuestion extends Model
{
  use HasFactory;

  protected $table = 'questionnaire_questions';

  protected $fillable = [
    'title',
    'heading',
    'description'
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
}
