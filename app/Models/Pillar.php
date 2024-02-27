<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\ApprovalFlow;
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
      'auto_approve',
      'auto_submit_no_tasks',
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
    "auto_submit_no_tasks" => "boolean",
    "auto_approve_no_tasks" => "boolean",
    "submission_expires" => "boolean",
    "tasks" => "json"
  ];

  public function questionnaire(): BelongsTo {
    return $this->belongsTo(Questionnaire::class);
  }

  public function approval_flow(): BelongsTo {
    return $this->belongsTo(ApprovalFlow::class);
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

      // $relevantJson["tasks"] = $tasks;
    } else {
      $relevantJson["tasks"] = [];
    }

    $this->fill($relevantJson);  

    $q = Questionnaire::firstOrNew(["name" => $this->name]);
    $q->importFromJson($jsonArr["questionnaire"]);
    $q->name = $this->name; // Override name with Pillar name
    $q->save();
    $this->questionnaire_id = $q->id;    

    /**
     * Grab our approval flow
     */    
    $approvalFlowName = $jsonArr["approval_flow"]["name"];
    $approvalFlow = ApprovalFlow::where(['name' => $approvalFlowName])->first();
    $this->approval_flow_id = $approvalFlow->id;

    $this->save();
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
