<?php

namespace App\Models;

use App\Models\ActionField;
use App\Models\InputField;
use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Questionnaire extends Model
{
    use HasFactory;

    protected $table = 'questionnaires';

    protected $fillable = [
        'name',
        'type',
        'risk_calculation',
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(QuestionnaireQuestion::class);
    }

    /**
     * Populate our variables from a JSON array
     */
    public function importFromJson($jsonArr)
    {
        Log::Info("Questionnaire.importFromJson()");
        // Strip our the extra JSON fields not related to this object
        $relevantJson = array_filter($jsonArr, function ($k) {
            return in_array($k, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        $this->fill($relevantJson);
        $this->save();

        $sort_order = 0;
        foreach ($jsonArr["questions"] as $question) {
            $q = QuestionnaireQuestion::firstOrNew([
                "questionnaire_id" => $this->id,
                "title" => $question["title"],
            ]);

            if (!array_key_exists("sort_order", $question)) {
                $question["sort_order"] = $sort_order++;
            }

            $q->importFromJson($question, $this->id);
        }
    }

    /**
     * Convert a JSON input string into a questionnaire
     */
    public function importFromSDLTJson($jsonInput)
    {
        $this->name = $jsonInput->name;
        $this->type = $jsonInput->type;
        $this->save();

        $questionSortOrder = 0;
        foreach ($jsonInput->questions as $question) {
            $q = new QuestionnaireQuestion();
            $q->questionnaire_id = $this->id;
            $q->title = $question->title;
            $q->heading = $question->heading;
            $q->description = $question->description;
            $q->sort_order = $questionSortOrder++;
            $q->save();

            if (isset($question->answerInputFields)) {
                foreach ($question->answerInputFields as $inputField) {
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
                foreach ($question->answerActionFields as $actionField) {
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
