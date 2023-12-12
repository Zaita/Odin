<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Questionnaire;

class Pillar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'pillars';

    protected $fillable = [
        'name',
        'caption',
        'key_information',
        'type',
        'risk_calculation',
        'icon',
        'auto_approve',
        'auto_approve_no_tasks',
        'submission_expires',
        'expire_after_dayes',
        'sort_order',
        'enabled',
    ];

    protected $casts = [
      'sort_order' => 'integer',
      'enabled' => 'boolean',
  ];

  public function questionnaire(): BelongsTo {
    return $this->belongsTo(Questionnaire::class);
  }

  public $errors = array();

    /**
     * Construct our pillar
     */
    public function __construct(array $attributes = array()) {
      parent::__construct($attributes);
    }


    public function importFromJson($jsonInput) {         
      $this->name = $jsonInput->name;
      $this->caption = $jsonInput->caption;
      $this->key_information = $jsonInput->key_information;
      $this->type = $jsonInput->risk_calculation;
      // $this->auto_approve = $json->

      // $table->id();
      // $table->string('name');
      // $table->string('caption');
      // $table->enum('type', ['questionnaire', 'risk_questionnaire'])->default('questionnaire');
      // $table->string('icon')->default('none');                       
      // $table->text('key_information');
      // $table->json('questions');      
      // $table->enum('risk_calculation', ['none', 'zaita_approx', 'highest_value'])->default('none');
      // $table->boolean('auto_approve')->default(false);
      // $table->boolean('auto_approve_no_tasks')->default(false);
      // $table->boolean('submission_expires')->default(false);
      // $table->unsignedInteger('expire_after_days')->default(0);                        
      // $table->unsignedInteger('sort_order')->default(9999);
      // $table->timestamps();
    }

    /**
     * Override save to set the index on every question.
     * We'll use this for identifying questions better later when
     * we're looking at editing them
     */
    public function sssave(array $options = []) {
      /**
       * Find/Replace some text from old SDLT language
       */
      // Log::Info(json_decode($this->questions));
      // $questionText = $this->questions;
      // $questionText = str_replace("isProductName", "productName", $questionText);
      // $questionText = str_replace("isBusinessOwner", "businessOwner", $questionText);
      // $questionText = str_replace("isTicketLink", "ticketUrl", $questionText);


      
      /**
       * Add the index values to our questions and input fields
       * based on the order we're saving them.
       */
      // $i = 0;
      // $questions = json_decode($questionText);
      // foreach($questions as &$question) {
      //   $question->index = $i++;

      //   if (isset($question->answerInputFields)) {
      //     $inputIndex = 0;
      //     foreach($question->answerInputFields as $inputField) {
      //       $inputField->index = $inputIndex++;
      //     }
      //   }
      // }

      /**
       * Validate some business logic
       */
      // $productNameCount = 0;
      // $businessOwnerCount = 0;
      // $ticketUrlCount = 0;
      // foreach($questions as &$question) {
      //   if (isset($question->answerInputFields)) {
      //     foreach($question->answerInputFields as &$inputField) {
      //       /**
      //        * Clean up where the input type cannot possibly match a key field
      //        */
      //       $type = $inputField->inputType;
      //       Log::Info("InputType: ${type}");
      //       switch($inputField->inputType) {
      //         case "text":
      //           $inputField->businessOwner = false;
      //           $inputField->ticketUrl = false;
      //           break;
      //         case "email":
      //           $inputField->productName = false;
      //           $inputField->ticketUrl = false;
      //           break;
      //         case "url":
      //           $inputField->productName = false;
      //           $inputField->businessOwner = false;
      //           break;
      //         default:
      //         $inputField->productName = false;
      //         $inputField->businessOwner = false;
      //         $inputField->ticketUrl = false;
      //         break;
      //       }

      //       $productNameCount = isset($inputField->productName) && $inputField->productName == true ? $productNameCount + 1 : $productNameCount;
      //       $businessOwnerCount = isset($inputField->businessOwner) && $inputField->businessOwner == true ? $businessOwnerCount + 1 : $businessOwnerCount;
      //       $ticketUrlCount = isset($inputField->ticketUrl) && $inputField->ticketUrl == true ? $ticketUrlCount + 1 : $ticketUrlCount;
      //     }
      //   }
      // }
      // if ($productNameCount > 1) {
      //   $this->errors["productName"] = "Product name has already been defined on this questionnaire";        
      // } 
      // if ($businessOwnerCount > 1) {
      //   $this->errors["businessOwner"] = "Business owner has already been defined on this questionnaire";
      // }
      // if ($ticketUrlCount > 1) {
      //   $this->errors["ticketUrl"] = "Ticket url has already been defined on this questionnaire";  
      // }

      // if (count($this->errors) > 0)
      //   return false;

      // Save
      // $this->questions = json_encode($questions);
      return parent::save($options);
    }

    /**
     * Update our answer action input field
     */
    public function updateInputField(&$errors, $questionId, $inputId, $request) {
      $questions = json_decode($this->questions, true);
      $question = $questions[$questionId];  
      $inputField = $question['answerInputFields'][$inputId];

      Log::Info("Before:");
      Log::Info($inputField);
      $merger = $request->validated();
      Log::Info($merger);
      $inputField = array_merge($inputField, $request->validated());
      Log::Info("After:");
      Log::Info($inputField);

      $questions[$questionId]['answerInputFields'][$inputId] = $inputField;
      Log::Info($questions);
      $this->questions = json_encode($questions);
      return $this->save();
    }
}
