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

  protected $table = 'pillars';

  protected $fillable = [
      'name',
      'caption',
      'icon',
      'key_information',
      'risk_calculation',     
      'auto_approve',
      'auto_approve_no_tasks',
      'submission_expires',
      'expire_after_dayes',
      'enabled',
      'tasks',
  ];

  protected $hidden = [
    "sort_order",
    "questionnaire_id",
    "approval_flow_id",
    "created_at",
    "updated_at"
  ];

  protected $casts = [
    'sort_order' => 'integer',
    'enabled' => 'boolean',
    "auto_approve" => "boolean",
    "auto_approve_no_tasks" => "boolean",
    "submission_expires" => "boolean",
    "tasks" => "string"
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


  public function importFromJson($jsonArr) { 
    Log::Info("pillar.importFromJson()");
    // Strip our the extra JSON fields not related to this object
    $relevantJson = array_filter($jsonArr, function($k) { 
      return in_array($k, $this->fillable);
    }, ARRAY_FILTER_USE_KEY);
    
    /**
     * Check if the associated tasks exists or not.
     * If they don't exist, we'll create a blank questionnaire
     * as a placeholder
     */
    if (isset($jsonArr["tasks"])) {
      $tasks = $jsonArr["tasks"];
      foreach($tasks as $task) {
        $t = Task::firstOrNew(["name" => $task["name"]]);
        $t->defaultSetupIfNew($task["name"]);        
      }

      $relevantJson["tasks"] = json_encode($jsonArr["tasks"]);
    } else {
      $relevantJson["tasks"] = "{}";
    }

    $this->fill($relevantJson);  

    $q = Questionnaire::firstOrNew(["name" => $this->name]);
    $q->importFromJson($jsonArr["questionnaire"]);
    $q->name = $this->name; // Override name with Pillar name
    $q->save();
    $this->questionnaire_id = $q->id;    

    $this->save();
  }

    /**
     * Override save to set the index on every question.
     * We'll use this for identifying questions better later when
     * we're looking at editing them
     */
    public function sssave(array $options = []) {


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

      // $inputField = 

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
