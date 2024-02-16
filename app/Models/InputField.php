<?php

namespace App\Models;

use App\Models\CheckboxOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use App\Models\QuestionnaireQuestion;

class InputField extends Model
{
    use HasFactory;

    protected $table = 'input_fields';

    public $errors = array();

    protected $fillable = [
        'label',
        'required',
        'input_type',
        'min_length',
        'max_length',
        'placeholder',
        'product_name',
        'business_owner',
        'release_date',
        'ticket_url',
        'config',
    ];

    protected $hidden = [
        "questionnaire_question_id",
        "created_at",
        "updated_at",
    ];

    protected $casts = [
        "required" => "boolean",
        "product_name" => "boolean",
        "business_owner" => "boolean",
        "release_date" => "boolean",
        "ticket_url" => "boolean",
    ];

    public function checkbox_options(): HasMany
    {
        return $this->hasMany(CheckboxOption::class);
    }

    /**
     * Import An InputField from JSON
     */
    public function importFromJson($jsonArr, $questionId)
    {
        Log::Info("InputField.importFromJson()");

        // Strip our the extra JSON fields not related to this object
        $relevantJson = array_filter($jsonArr, function ($k) {
            return in_array($k, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        $this->fill($relevantJson);
        $this->questionnaire_question_id = $questionId;
        $this->save();

        /**
         * Check if this input type is a checkbox, if so. Create
         * the checkbox_options
         */
        // echo (sprintf("Input Type: %s\n", $this->input_type));
        if ($this->input_type == "checkbox" && isset($jsonArr["checkbox_options"])) {
            $options = $jsonArr["checkbox_options"];
            foreach ($options as $checkbox_option) {
                $option = new CheckboxOption($checkbox_option);
                $option->risks = isset($checkbox_option["risks"]) ? json_encode($checkbox_option["risks"]) : null;
                $option->input_field_id = $this->id;
                $option->save();
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
    public function isValid(QuestionnaireQuestion $question)
    {
        if (!isset($question->inputFields)) {
            Log::Info("Question has no Input Fields");
            return true;
        }

        Log::Info("Checking if InputField.isValid()");

        $type = $this->input_type;
        Log::Info("InputType: $type");
        switch ($this->input_type) {
            case "text":
                $this->business_owner = false;
                $this->ticket_url = false;
                $this->release_date = false;
                break;
            case "email":
                $this->product_name = false;
                $this->ticket_url = false;
                $this->release_date = false;
                break;
            case "url":
                $this->product_name = false;
                $this->business_owner = false;
                $this->release_date = false;
                break;
            case "date":
                $this->product_name = false;
                $this->business_owner = false;
                $this->ticket_url = false;
                break;
            default:
                $this->product_name = false;
                $this->business_owner = false;
                $this->ticket_url = false;
                $this->release_date = false;
                break;
        }

        $productNameCount = 0;
        $businessOwnerCount = 0;
        $ticketUrlCount = 0;
        $releaseDateCount = 0;
        foreach ($question->inputFields as $inputField) {
            /**
             * Clean up where the input type cannot possibly match a key field
             */
            $productNameCount = isset($inputField->product_name) && $inputField->product_name == true ? $productNameCount + 1 : $productNameCount;
            $businessOwnerCount = isset($inputField->business_owner) && $inputField->business_owner == true ? $businessOwnerCount + 1 : $businessOwnerCount;
            $ticketUrlCount = isset($inputField->ticket_url) && $inputField->ticket_url == true ? $ticketUrlCount + 1 : $ticketUrlCount;
            $releaseDateCount = isset($inputField->release_date) && $inputField->release_date == true ? $releaseDateCount + 1 : $releaseDateCount;
        }

        if ($this->productName && $productNameCount >= 1) {
            $this->errors["product_name"] = "Product name has already been defined on this questionnaire";
        }
        if ($this->business_owner && $businessOwnerCount >= 1) {
            $this->errors["business_owner"] = "Business owner has already been defined on this questionnaire";
        }
        if ($this->ticket_url && $ticketUrlCount >= 1) {
            $this->errors["ticket_url"] = "Ticket url has already been defined on this questionnaire";
        }
        if ($this->release_date && $releaseDateCount >= 1) {
            $this->errors["release_date"] = "Release date has already been defined on this questionnaire";
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

}
