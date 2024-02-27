<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\ActionField;
use App\Models\ApprovalFlow;
use App\Models\InputField;
use App\Models\Pillar;
use App\Models\Risk;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\InputOption;
use App\Models\ImpactThreshold;
use App\Models\Task;
use App\Models\QuestionnaireRisk;
use App\Http\Requests\PillarCreateRequest;
use App\Http\Requests\PillarSaveRequest;
use App\Http\Requests\InputFieldRequest;
use App\Http\Requests\ActionFieldRequest;
use App\Http\Requests\QuestionnaireQuestionRequest;

class PillarController extends Controller
{
  /**
   * GET /admin/content/pillars
   */
  public function index(Request $request) {
    return Inertia::render('Admin/Content/Pillars', [
      'siteConfig' => Configuration::site_config(),
      'pillars' => Pillar::orderBy('created_at')->paginate(20),
    ]); 
  }

  /**
   * GET /admin/content/pillar/add
   * Load the add screen
   */
  public function add(Request $request) {
    $approvalFlows = ApprovalFlow::select("name")->get();
    $approvalFlowOptions = array();
    foreach( $approvalFlows as $approvalFlow ) {
      array_push($approvalFlowOptions, $approvalFlow->name);
    }

    return Inertia::render('Admin/Content/Pillars/Pillar.Add', [
      'approvalFlowOptions' => $approvalFlowOptions,
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * Create a new pillar
   */
  public function create(PillarCreateRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Pillars.Add", $request);    
    $approvalFlowId = ApprovalFlow::where(["name" => $request->input('approval_flow')])->first()->id;

    $q = Questionnaire::create($request->safe()->only(['name', 'type']));
    $p = new Pillar();
    $p->fill($request->safe()->except("type", "approval_flow"));
    $p->questionnaire_id = $q->id;   
    $p->approval_flow_id = $approvalFlowId;    
    $p->save();
    return Redirect::route('admin.content.pillar.edit', $p->id)
      ->with('saveOk', 'New pillar created successfully');
  }

  /**
   * Delete a group. We have some protected groups that we don't
   * allow to be removed as they are necessary for the approval
   * flows 
   */
  public function delete(Request $request)  {  
    AuditLog::Log("Content.Pillars.Delete", $request);
    $id = $request->input('id', -1);
    $pillar = Pillar::with(["questionnaire"])->findOrFail($id);
    $pillar->questionnaire->delete();
    $pillar->delete();
    return Redirect::route('admin.content.pillars');
  }

  /**
   * Download a pillar as a JSON file
   */
  public function download(Request $request, $pillarId)  {  
    AuditLog::Log("Content.Pillar($pillarId).Download", $request);
    return response()->streamDownload(
      function () use ($pillarId) { 
        $pillar = Pillar::with([
        "approval_flow",
        "questionnaire", 
        "questionnaire.questions" => function(Builder $q) {$q->orderBy('sort_order');},
        "questionnaire.questions.inputFields",
        "questionnaire.questions.inputFields.input_options",
        "questionnaire.questions.actionFields",
        ])->findOrFail($pillarId);
        echo json_encode($pillar, JSON_PRETTY_PRINT);
      }
    ,'pillar.txt');
  }

  /**
   * **************************************************************************
   * Below we handle specific routes for working with a singular pillar
   * e.g. adding/modifying questions
   * **************************************************************************
   */

  /**
   * Load the edit screen for an existing pillar
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Content.Pillar.Edit", $request);
    $pillar = Pillar::with(["questionnaire", "approval_flow"])->findOrFail($id); 

    $approvalFlows = ApprovalFlow::select("name")->get();
    $approvalFlowOptions = array();
    foreach( $approvalFlows as $approvalFlow ) {
      array_push($approvalFlowOptions, $approvalFlow->name);
    }

    return Inertia::render('Admin/Content/Pillars/Pillar.Edit', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'approvalFlowOptions' => $approvalFlowOptions,
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * POST /admin/content/pillar/{id}/save
   * Save changes to our existing group
   */
  public function save(PillarSaveRequest $request, $pillarId) : RedirectResponse {
    AuditLog::Log("Content.Pillar($pillarId).Save", $request);
    $pillar = Pillar::findOrFail($pillarId); 
    $pillar->update($request->safe()->except(["type", "risk_calculation", "custom_risks"]));
    
    $questionnaire = Questionnaire::findOrFail($pillar->questionnaire_id);
    $questionnaire->update($request->safe()->only(["type","risk_calculation", "custom_risks"]));
    $questionnaire->save();

    $approvalFlowId = ApprovalFlow::where(["name" => $request->input('approval_flow')])->first()->id;
    $pillar->approval_flow_id = $approvalFlowId;
    
    if (!$pillar->save()) {
      return back()->withInput()->withErrors($pillar->errors);
    }

    return Redirect::route('admin.content.pillar.edit', $pillarId)
      ->with('saveOk', 'Pillar updated successfully');
  }

  /**
   * Load the list of questions for the pillar
   */
  public function pillar_questions(Request $request, $id) {
    $pillar = Pillar::with(["questionnaire", 
      "questionnaire.questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($id);
    
    return Inertia::render('Admin/Content/Pillars/Pillar.Questions', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
    ]); 
  }

  /**
   * Load the add screen for adding a new question to a pillar
   */
  public function pillar_questions_add(Request $request, $id) {
    return Inertia::render('Admin/Content/Pillars/Pillar.Question.Add', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => Pillar::findOrFail($id),
    ]); 
  }

  /**
   * Create the new question on the pillar based on the user
   * input supplied in the request
   */
  public function pillar_questions_create(QuestionnaireQuestionRequest $request, $id) {
    $pillar = Pillar::with(["questionnaire", "questionnaire.questions"])->findOrFail($id);

    $newQuestion = new QuestionnaireQuestion($request->validated());
    $newQuestion->sort_order = count($pillar->questionnaire->questions);
    $pillar->questionnaire->questions()->save($newQuestion);

    return Redirect::route('admin.content.pillar.questions', $pillar->id);
  }

  /**
   * Update the order of our questions in the pillar
   */
  public function pillar_questions_reorder(Request $request, $id) {
    $pillar = Pillar::with(["questionnaire", "questionnaire.questions"])->findOrFail($id);
    
    $questions = $pillar->questionnaire->questions;
    $newOrder = $request->input('newOrder'); 
    for ($i = 0; $i < count($newOrder); $i++) {
      for ($j = 0; $j < count($questions); $j++) {
        if ($questions[$j]->id == $newOrder[$i]) {
          $pillar->questionnaire->questions[$j]->sort_order = $i;
          $pillar->questionnaire->questions[$j]->save();
          continue 2;
        }
      }      
    }

    return Redirect::route('admin.content.pillar.questions', $pillar->id);
  }

  /**
   * **************************************************************************
   * Below we handle specific routes for working with a singular question on a pillar
   * e.g. modifying content, adding input/action fields
   * **************************************************************************
   */

  /**
   * Delete a question
   */
  public function pillar_question_delete(Request $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Delete", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->delete();    
    return Redirect::route('admin.content.pillar.questions', $pillarId);
  }

  /**
   * Load the edit screen for adding a new question to a pillar
   */
  public function pillar_question_edit(Request $request, $id, $questionId) {
    $pillar = Pillar::findOrFail($id);
    $question = QuestionnaireQuestion::findorFail($questionId);   
    return Inertia::render('Admin/Content/Pillars/Pillar.Question.Edit', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }
  
  /**
   * Save changes to a question 
   */
  public function pillar_question_save(QuestionnaireQuestionRequest $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Update", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->update($request->validated());
    $question->save();
    return Redirect::route('admin.content.pillar.question.edit', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Load our "Inputs" screen for a question
   */
  public function pillar_question_inputs(Request $request, $id, $questionId) {
    AuditLog::Log("Content.Pillar($id).Question($questionId).Inputs", $request);
    $pillar = Pillar::findOrFail($id);
    $question = QuestionnaireQuestion::with(['inputFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Pillars/Questions/Question.Inputs', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }

  /**
   * Update the order of the input fields for our question
   */
  public function pillar_question_inputs_reorder(Request $request, $pillarId, $questionId) {
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);

    $inputFields = $question->inputFields;
    $newOrder = $request->input('newOrder'); 
    for ($i = 0; $i < count($newOrder); $i++) {
      for ($j = 0; $j < count($inputFields); $j++) {
        if ($inputFields[$j]->id == $newOrder[$i]) {
          $question->inputFields[$j]->sort_order = $i;
          $question->inputFields[$j]->save();
          continue 2;
        }
      }      
    }

    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Load the Pillar->Questionnaire->Question->Inputs->Add Screen
   */
  public function pillar_question_input_add(Request $request, $pillarId, $questionId) {
    $pillar = Pillar::findOrFail($pillarId);
    $question = QuestionnaireQuestion::findOrFail($questionId);

    return Inertia::render('Admin/Content/Pillars/Questions/Input.Add', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }

  /**
   * Handle the POST back adding a new Input Field to our Question
   */
  public function pillar_question_input_create(InputFieldRequest $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar.Question.Input.Create", $request);    
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    
    $newInput = new InputField($request->validated());
    $newInput->sort_order = count($question->inputFields);
    if (!$newInput->isValid($question)) {
      return back()->withInput()->withErrors($newInput->errors);  
    }
    $question->inputFields()->save($newInput);

    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Load the Pillar->Questionnaire->Question->Inputs->Edit Screen
   */
  public function pillar_question_input_edit(Request $request, $pillarId, $questionId, $inputId) {
    $pillar = Pillar::with(["questionnaire"])->findOrFail($pillarId); 

    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    $inputField = InputField::with("input_options")->findOrFail($inputId);

    if (!$pillar->questionnaire->custom_risks) {
      $risks = Risk::all();
    } else {
      $risks = QuestionnaireRisk::where(['questionnaire_id' => $pillar->questionnaire->id])->get();
    }

    return Inertia::render('Admin/Content/Pillars/Questions/Input.Edit', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
      'input' => $inputField,
      'risks' => $risks,
    ]); 
  }

  /**
   * Handle the POST back save an Input Field to our Question
   */
  public function pillar_question_input_save(InputFieldRequest $request, $pillarId, $questionId, $inputId) {
    AuditLog::Log("Content.Pillar.Question.Input.Save", $request);    
    $inputField = InputField::findOrFail($inputId);
    $inputField->update($request->validated());
    $inputField->save();

    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Handle the POST back to delete an input field on our question
   */
  public function pillar_question_input_delete(Request $request, $pillarId, $questionId, $inputId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Input($inputId).Delete", $request);    
    $inputField = InputField::findOrFail($inputId);
    $inputField->delete();   
    
    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * GET /admin/content/pillars/{pillarId}/question/{questionId}/inputs/{inputId}/checkbox/add
   * Add a new option to our checkbox. Don't bother loading an Add page, they can edit it
   * afterwards.
   */
  public function pillar_question_input_checkbox_add(Request $request, $pillarId, $questionId, $inputId) { 
    $option = new InputOption();
    $option->input_field_id = $inputId;
    $option->label = "New Option";
    $option->value = "New Option";
    $option->risks = "{}";
    $option->sort_order = 999;
    $option->save();

    return Redirect::route('admin.content.pillar.question.input.edit', 
      ["id" => $pillarId, "questionId" => $questionId, "inputId" => $inputId]);
  }

  /**
   * GET /admin/content/pillars/{pillarId}/question/{questionId}/inputs/{inputId}/checkbox/{optionId}/edit
   */
  public function pillar_question_input_checkbox_edit(Request $request, $pillarId, $questionId, $inputId, $optionId) {     
    $pillar = Pillar::findOrFail($pillarId);
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    $inputField = InputField::with("input_options")->findOrFail($inputId);    
    $option = InputOption::findOrFail($optionId);

    if (!$pillar->questionnaire->custom_risks) {
      $risks = Risk::all();
    } else {
      $risks = QuestionnaireRisk::where(['questionnaire_id' => $pillar->questionnaire->id])->get();
    }

    return Inertia::render('Admin/Content/Pillars/Questions/Inputs/Checkbox.AddEdit', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
      'field' => $inputField,
      'option' => $option,
      'risks' => $risks,
      'thresholds' => ImpactThreshold::all(),
    ]);
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/inputs/{inputId}/checkbox/{optionId}/save
   */
  public function pillar_question_input_checkbox_save(Request $request, $pillarId, $questionId, $inputId, $optionId) {     
    Log::Info("pillar_question_input_checkbox_save");
    $option = InputOption::findOrFail($optionId);
    $option->validateAnswers($request->all());

    return back()->withInput()->withErrors($option->errors);
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/inputs/{inputId}/checkbox/delete
   * The request parameter is the Id of the checkbox option
   */
  public function pillar_question_input_checkbox_delete(Request $request, $pillarId, $questionId, $inputId) { 
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Input($inputId).InputOption.Delete", $request);    
    $optionId = $request->input('input_option_id', null);
    if (!is_null($optionId)) {
      $option = InputOption::findOrFail($optionId);
      $option->delete();
    }

    return Redirect::route('admin.content.pillar.question.input.edit', ["id" => $pillarId, "questionId" => $questionId, "inputId" => $inputId]);
  }

  /**
   * **************************************************************************
   * The following section deals with working with actions on a question
   * **************************************************************************
   */


  /**
  * GET /admin/content/pillars/{pillarId}/question/{questionId}/actions
  * Load the current actions for the question
  */
  public function pillar_question_actions(Request $request, $pillarId, $questionId) {
    $pillar = Pillar::findOrFail($pillarId);
    $question = QuestionnaireQuestion::with(['actionFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Pillars/Questions/Question.Actions', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/actions/reorder
   * Update the order of the action fields for our question
   */
  public function pillar_question_actions_reorder(Request $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Actions.Reorder", $request);
    $question = QuestionnaireQuestion::with('actionFields')->findOrFail($questionId);

    $fields = $question->actionFields;
    $newOrder = $request->input('newOrder'); 
    for ($i = 0; $i < count($newOrder); $i++) {
      for ($j = 0; $j < count($fields); $j++) {
        if ($fields[$j]->id == $newOrder[$i]) {
          $question->actionFields[$j]->sort_order = $i;
          $question->actionFields[$j]->save();
          continue 2;
        }
      }      
    }

    return Redirect::route('admin.content.pillar.question.actions', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * GET /admin/content/pillars/{pillarId}/question/{questionId}/action/add
   * Load the Pillar->Questionnaire->Question->Action->Add Screen
   */
  public function pillar_question_action_add(Request $request, $pillarId, $questionId) {
    $pillar = Pillar::with(["questionnaire",  "questionnaire.questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($pillarId);
    $question = QuestionnaireQuestion::findOrFail($questionId);

    $questionTitles = array([""]);
    foreach($pillar->questionnaire->questions as $Actionquestion) {
      array_push($questionTitles, $Actionquestion->title);
    }

    $tasks = Task::all();
    $taskNames = array([""]);
    foreach($tasks as $taskObj) {
      array_push($taskNames, $taskObj->name);
    }

    return Inertia::render('Admin/Content/Pillars/Questions/Action.Add', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
      'questionNames' => $questionTitles,
      'taskNames' => $taskNames,
    ]); 
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/action/create
   * Handle the POST back adding a new Action Field to our Question
   */
  public function pillar_question_action_create(ActionFieldRequest $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Action.Create", $request);    
    $pillar = Pillar::findOrFail($pillarId);

    $question = QuestionnaireQuestion::with('actionFields')->findOrFail($questionId);
    
    $newField = new ActionField($request->safe()->except("tasks"));
    $taskArr = array();
    $tasks = $request->input('tasks', []);
    foreach($tasks as $task) {
      array_push($taskArr, ["name" => $task["value"]]);
    }       
    
    $newField->tasks = $taskArr; //json_encode($taskArr);
    $newField->sort_order = count($question->actionFields);
    if (!$newField->isValid($question)) {
      return back()->withInput()->withErrors($newField->errors);  
    }
    $question->actionFields()->save($newField);

    return Redirect::route('admin.content.pillar.question.actions', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * GET /admin/content/pillars/{pillarId}/question/{questionId}/action/{actionId}/edit
   * Load the Pillar->Questionnaire->Question->Actions->Edit Screen
   */
  public function pillar_question_action_edit(Request $request, $pillarId, $questionId, $actionId) {
    $pillar = Pillar::findOrFail($pillarId);
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    $actionField = ActionField::findOrFail($actionId);  
    
    $questionTitles = array([""]);
    foreach($pillar->questionnaire->questions as $Actionquestion) {
      array_push($questionTitles, $Actionquestion->title);
    }

    $tasks = Task::all();
    $taskNames = array([""]);
    foreach($tasks as $taskObj) {
      array_push($taskNames, $taskObj->name);
    }

    return Inertia::render('Admin/Content/Pillars/Questions/Action.Edit', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'question' => $question,
      'action' => $actionField,
      'questionNames' => $questionTitles,
      'taskNames' => $taskNames,
    ]); 
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/action/{actionId}/save
   * Handle the POST back adding a new Action Field to our Question
   */
  public function pillar_question_action_save(ActionFieldRequest $request, $pillarId, $questionId, $actionId) {
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Action($actionId).Save", $request);    
    $question = QuestionnaireQuestion::with('actionFields')->findOrFail($questionId);
    
    $newField = ActionField::findOrFail($actionId);
    $newField->update($request->safe()->except("tasks"));
    $taskArr = array();
    $tasks = $request->input('tasks', []);
    foreach($tasks as $task) {
      array_push($taskArr, ["name" => $task["value"]]);
    }       
    
    $newField->tasks = $taskArr; //json_encode($taskArr);
    $newField->sort_order = count($question->actionFields);
    if (!$newField->isValid($question)) {
      return back()->withInput()->withErrors($newField->errors);  
    }
    $question->actionFields()->save($newField);

    return Redirect::route('admin.content.pillar.question.actions', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * POST /admin/content/pillars/{pillarId}/question/{questionId}/action/{actionId}/delete
   * Delete the target action from our question
   */
  public function pillar_question_action_delete(Request $request, $pillarId, $questionId, $actionId) { 
    AuditLog::Log("Content.Pillar($pillarId).Question($questionId).Action($actionId).Delete", $request);    
    $actionField = ActionField::findOrFail($actionId);
    $actionField->delete();

    return Redirect::route('admin.content.pillar.question.actions', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * **************************************************************************
   * The following section deals with working with tasks on a pillar
   * **************************************************************************
   */

  /**
   * Handle Tasks on our Pillar
   */
  public function pillar_tasks(Request $request, $pillarId) {
    $pillar = Pillar::with("questionnaire")->findOrFail($pillarId);
    $tasks = Task::select('name')->get();
    $taskOptions = array();
    foreach( $tasks as $task ) {
      array_push($taskOptions, $task->name);
    }

    $linkedTasks = array();
    if (!is_null($pillar->tasks)) {
      foreach($pillar->tasks as $requiredTask) {
        $taskName = $requiredTask["name"];
        $task = Task::where(["name" => $taskName])->first();
        array_push($linkedTasks, $task);
      }
    }
  
    return Inertia::render('Admin/Content/Pillars/Pillar.Tasks', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'linkedTasks' => $linkedTasks,
      'taskOptions' => $taskOptions,
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  public function pillar_task_link(Request $request, $pillarId) {
    $taskName = $request->input('task', '');
    Log::Info("Linking $taskName to Pillar $pillarId");

    $pillar = Pillar::findOrFail($pillarId);
    $task = Task::where(["name"=> $taskName])->first();
    if ($task == null) {
      return Redirect::route('admin.content.pillar.tasks', ["id" => $pillarId])
      ->with('saveOk', 'Task does not exist');
    }

    if (is_null($pillar->tasks)) {
      $pillar->tasks = array();
    }
    
    foreach($pillar->tasks as $requiredTask) {
      if ($requiredTask["name"] == $taskName) {
        return Redirect::route('admin.content.pillar.tasks', ["id" => $pillarId])
        ->with('saveOk', 'Task has already been linked to this pillar');
      }
    }

    $taskList = $pillar->tasks;
    array_push($taskList, ["name" => $taskName]);
    $pillar->tasks = json_decode(json_encode($taskList));
    $pillar->save();

    return Redirect::route('admin.content.pillar.tasks', ["id" => $pillarId])
      ->with('saveOk', 'Task has been linked successfully to pillar');
  }

  public function pillar_task_unlink(Request $request, $pillarId, $taskId) {
    $pillar = Pillar::findOrFail($pillarId);
    $task = Task::findOrFail($taskId);

    $taskName = $task->name;
    $taskList = array();
    foreach($pillar->tasks as $requiredTask) {
      if ($requiredTask["name"] != $taskName) {
        array_push($taskList, $requiredTask);
      }
    }
    $pillar->tasks = json_decode(json_encode($taskList));
    $pillar->save();

    return Redirect::route('admin.content.pillar.tasks', ["id" => $pillarId])
      ->with('saveOk', 'Task has been unlinked successfully from pillar');
  }

  /**
   * **************************************************************************
   * The following section deals with working with risks on a pillar
   * **************************************************************************
   */

  /**
   * GET /admin/content/pillar/{pillarId}/risks
   * Load the list of risks for this pillar
   */
  public function pillar_risks(Request $request, $pillarId) {
    $pillar = Pillar::with(["questionnaire"])->findOrFail($pillarId);
    $risks = QuestionnaireRisk::where(['questionnaire_id' => $pillar->questionnaire->id])->get();
  
    return Inertia::render('Admin/Content/Pillars/Pillar.Risks', [
      'siteConfig' => Configuration::site_config(),
      'pillar' => $pillar,
      'risks' => $risks,
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * POST /admin/content/pillar/{pillarId}/risk/create
   */
  public function pillar_risk_create(Request $request, $pillarId) {
    AuditLog::Log("Admin.Content.Pillar($pillarId).Risk.Create", $request);
    $pillar = Pillar::with(["questionnaire"])->findOrFail($pillarId);
    $riskName = $request->input('name', '');
    $riskDescription = $request->input('description', '');
    Log::Info("Adding $riskName to Pillar $pillarId");

    $risk = QuestionnaireRisk::firstOrNew(["name" => $riskName]);
    $risk->description = $riskDescription;
    $risk->questionnaire_id = $pillar->questionnaire->id;
    $risk->save();
    
    return Redirect::route('admin.content.pillar.risks', ["id" => $pillarId])
      ->with('saveOk', 'Risk has been created successfully on pillar');
  }

  /**
   * POST /admin/content/pillar/{pillarId}/risk/{riskId}/delete
   */
  public function pillar_risk_delete(Request $request, $pillarId, $riskId) {
    $risk = QuestionnaireRisk::findOrFail($riskId);
    $risk->delete();

    return Redirect::route('admin.content.pillar.risks', ["id" => $pillarId])
      ->with('saveOk', 'Risk has been deleted successfully from pillar');
  }
};