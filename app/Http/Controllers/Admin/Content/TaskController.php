<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\DSRATaskSaveRequest;
use App\Http\Requests\TaskSaveRequest;
use App\Models\SecurityCatalogue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Task;
use App\Models\Group;
use App\Http\Requests\TaskRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Http\Requests\InputFieldRequest;
use App\Models\InputField;
use App\Models\InputOption;
use App\Models\Risk;
use App\Models\ImpactThreshold;
use App\Models\QuestionnaireRisk;

use App\Http\Requests\QuestionnaireQuestionRequest;
use App\Models\SecurityRiskAssessment;

class TaskController extends Controller
{
  /**
   * GET /admin/content/tasks
   * Load the list of tasks
   */
  public function index(Request $request) {
    return Inertia::render('Admin/Content/Tasks', [
      'siteConfig' => Configuration::site_config(),
      'tasks' => Task::orderBy('created_at')->paginate(20)
    ]); 
  }

  /**
   * GET /admin/content/task/add
   * Load the screen to add a new task
   */
  public function add(Request $request) {
    return Inertia::render('Admin/Content/Tasks/Task.Add', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * POST /admin/content/task/add
   * Create a new task
   */
  public function create(TaskRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Tasks.Create", $request);  
    
    $task = Task::firstOrNew(["name" => $request->safe()->only("name")]);
    if (!is_null($task->id)) {
      return back()->withInput()->withErrors(array("name" => "Name already exists, must be unique across all tasks"));
    }
    $task->fill($request->validated());
    $task->save();
    return Redirect::route('admin.content.task.edit', $task->id)->with('saveOk', 'New task added successfully');;
  }

  /**
   * GET /admin/content/task/edit/{id}
   * Load edit screen for a task
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Content.Task($id).Edit", $request);
    
    $task = Task::findOrFail($id);         
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      $groups = Group::all();
      return Inertia::render('Admin/Content/Tasks/Task.Questionnaire.Edit', [
        'siteConfig' => Configuration::site_config(),
        'task' => $task,
        'questionnaire' => Questionnaire::findOrFail($task->task_object_id),
        'groups' => $groups,
        'saveOk' => $request->session()->get('saveOk'),
      ]); 
    
    } else if ($task->type == "security_risk_assessment") {
      $sra = SecurityRiskAssessment::with("security_catalogue", "initial_risk_impact")->findOrFail($task->task_object_id);
      $riskQuestionnaires = Task::where('type', '=', 'risk_questionnaire')->get();
      return Inertia::render('Admin/Content/Tasks/Task.DSRA.Edit', [
        'siteConfig' => Configuration::site_config(),
        'task' => $task,
        'dsra' => $sra,
        'riskQuestionnaires' => $riskQuestionnaires,
        'securityCatalogues' => SecurityCatalogue::all(),
        'saveOk' => $request->session()->get('saveOk'),
      ]); 
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * POST /admin/content/task/edit/{id}
   * Save changes to an existing task
   */
  public function save(TaskSaveRequest $request, $id) {
    AuditLog::Log("Content.Task($id).Save", $request);
    $task = Task::findOrFail($id);
    $task->update($request->except("custom_risks"));
    $task->save();

    // If the task type is a questionnaire type, update the relevant fields too
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      $q = Questionnaire::findOrFail($task->task_object_id);
      $q->update($request->only(["type", "risk_calculation", "custom_risks"]));
      $q->save();
    }

    return Redirect::route('admin.content.task.edit', ["id" => $id])
      ->with('saveOk', 'Task updated successfully');
  }

  /**
   * POST /admin/content/task/edit/{id}
   * Save changes to an existing task (digital security risk assessment task type)
   */
  public function dsra_save(DSRATaskSaveRequest $request, $id) {
    AuditLog::Log("Content.Task($id).DSRA_Save", $request);
    $task = Task::findOrFail($id);
    $task->update($request->only(['name', 'key_information', 'time_to_review', 'time_to_complete']));
    $task->save();

    $dsra = SecurityRiskAssessment::findOrFaiL($task->task_object_id);
    $dsra->update($request->except(['time_to_review', 'key_information', 'time_to_complete']));
    $dsra->update_initial_risk_impact_task($request->only('initial_risk_impact'));
    Log::Info($dsra->key_information);
    $dsra->save();

    return Redirect::route('admin.content.task.edit', ["id" => $id])
      ->with('saveOk', 'Task updated successfully');
  }

  /**
   * Download a pillar as a JSON file
   */
  public function download(Request $request, $taskId)  {  
    AuditLog::Log("Content.Task($taskId).Download", $request);
    return response()->streamDownload(
      function () use ($taskId) { 
        $task = Task::findOrFail($taskId);
        if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {          
          $q = Questionnaire::with([
            "questions" => function(Builder $q) {$q->orderBy('sort_order');},
            "questions.inputFields",
            "questions.inputFields.input_options",
            "questions.actionFields"
          ])->findOrFail($task->task_object_id);

          $task->questionnaire = $q;
          echo json_encode($task, JSON_PRETTY_PRINT);
        }
      }
    ,'task.txt');
  }

  /**
   * GET /admin/content/task/{id}/questions
   * Load the list of questions for a task
   */
  public function questions(Request $request, $id) {
    AuditLog::Log("Content.Task($id).Questions", $request);
    
    $task = Task::findOrFail($id);      
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      if ($task->task_object_id == 0) {
        Log::error("Task didn't have a correct task_object_id");
      }
      $task->questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      return Inertia::render('Admin/Content/Tasks/Task.Questionnaire.Questions', [
        'siteConfig' => Configuration::site_config(),
        'task' => $task,
        'saveOk' => $request->session()->get('saveOk'),
      ]); 

    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * GET /admin/content/task/{id}/question/add
   * Load the screen to add a new question
   */
  public function question_add(Request $request, $id) {
    AuditLog::Log("Content.Task($id).Question.Add", $request);
    $task = Task::findOrFail($id);
    
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      $task->questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      return Inertia::render('Admin/Content/Tasks/Questions/Question.Add', [
        'siteConfig' => Configuration::site_config(),
        'task' => $task,
      ]); 
    
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * POST /admin/content/task/{id}/question/create
   * Create a new question
   */
  public function question_create(QuestionnaireQuestionRequest $request, $id) {
    AuditLog::Log("Content.Task($id).Question.Create", $request);
    $task = Task::findOrFail($id);
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      $questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      $newQuestion = new QuestionnaireQuestion($request->validated());
      $newQuestion->sort_order = count($questionnaire->questions);
      $questionnaire->questions()->save($newQuestion);

      return Redirect::route('admin.content.task.question.edit', ["id" => $id, "questionId" => $newQuestion->id])
        ->with("saveOk", "New question created successfully");
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * POST /admin/content/task/{id}/questions/reorder
   * Update the order of our questions in the pillar
   */
  public function questions_reorder(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($id);
    
    if ($task->type == "questionnaire" || $task->type == "risk_questionnaire") {
      $questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      $questions = $questionnaire->questions;
      $newOrder = $request->input('newOrder'); 
      for ($i = 0; $i < count($newOrder); $i++) {
        for ($j = 0; $j < count($questions); $j++) {
          if ($questions[$j]->id == $newOrder[$i]) {
            $questionnaire->questions[$j]->sort_order = $i;
            $questionnaire->questions[$j]->save();
            continue 2;
          }
        }      
      }
    }

    return Redirect::route('admin.content.task.questions', $task->id);
  }

  /**
   * **************************************************************************
   * Below we handle specific routes for working with a singular question on a pillar
   * e.g. modifying content, adding input/action fields
   * **************************************************************************
   */

  /**
   * Load the edit screen for adding a new question to a pillar
   */
  public function question_edit(Request $request, $taskId, $questionId) {
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Tasks/Questions/Question.Edit', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'question' => $question,
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * Save changes to a question 
   */
  public function question_save(QuestionnaireQuestionRequest $request, $id, $questionId) {
    AuditLog::Log("Content.Task($id).Question($questionId).Update", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->update($request->validated());
    $question->save();
    return Redirect::route('admin.content.task.question.edit', ["id" => $id, "questionId" => $questionId])
      ->with("saveOk", "Question updated successfully");
  }

  /**
   * POST /admin/content/task/{id}/question/{questionid}/delete
   * Delete the target question
   */
  public function question_delete(Request $request, $id, $questionId) {
    AuditLog::Log("Content.Task($id).Question($questionId).Delete", $request);
    $task = Task::findOrFail($id);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->delete();    
    return Redirect::route('admin.content.task.questions', $task->id)
      ->with("saveOk", "Question deleted successfully");
  }


  /**
   * **************************************************************************
   * Below we handle specific routes for working with inputs on a risk/questionnaire task
   * 
   * **************************************************************************
   */

  /**
   * GET /admin/content/task/{id}/question/{questionid}/inputs
   * Load our "Inputs" screen for a question
   */
  public function question_inputs(Request $request, $taskId, $questionId) {
    AuditLog::Log("Content.Task($taskId).Question($questionId).Inputs", $request);
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::with(['inputFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Tasks/Questions/Question.Inputs', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'question' => $question,
    ]); 
  }

  /**
   * Update the order of the input fields for our question
   */
  public function question_inputs_reorder(Request $request, $taskId, $questionId) {
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

    return Redirect::route('admin.content.task.question.inputs', ["id" => $taskId, "questionId" => $questionId]);
  }

  /**
   * GET /admin/content/task/{id}/question/{questionId}/input/add
   */
  public function question_input_add(Request $request, $taskId, $questionId) {
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::with(['inputFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Tasks/Questions/Input.Add', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'question' => $question,
    ]); 
  }

  /**
   * POST /admin/content/task/{id}/question/{questionId}/input/create
   * Handle the POST back adding a new Input Field to our Question
   */
  public function question_input_create(InputFieldRequest $request, $taskId, $questionId) {
    AuditLog::Log("Content.Pillar.Question.Input.Create", $request);    
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    
    $newInput = new InputField($request->validated());
    $newInput->sort_order = count($question->inputFields);
    if (!$newInput->isValid($question)) {
      return back()->withInput()->withErrors($newInput->errors);  
    }
    $question->inputFields()->save($newInput);

    return Redirect::route('admin.content.task.question.input.edit', ["id" => $taskId, "questionId" => $questionId, "inputId" => $newInput->id])
    ->with("saveOk", "New input field created successfully");;
  }

  /**
   * GET /admin/content/task/{id}/question/{questionId}/input/{inputId}/edit
   * Load edit screen for our input
   */
  public function question_input_edit(Request $request, $taskId, $questionId, $inputId) {
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    $inputField = InputField::with("input_options")->findOrFail($inputId);
    $risks = Risk::all();

    return Inertia::render('Admin/Content/Tasks/Questions/Input.Edit', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'question' => $question,
      'input' => $inputField,
      'risks' => $risks,
    ]); 
  }

  /**
   * Post /admin/content/task/{id}/question/{questionId}/input/{inputId}/save
   * Save changes to an existing input
   */
  public function question_input_save(InputFieldRequest $request, $taskId, $questionId, $inputId) {
    AuditLog::Log("Content.Task.Question.Input.Save", $request);    
    $inputField = InputField::findOrFail($inputId);
    $inputField->update($request->validated());
    $inputField->save();

    return Redirect::route('admin.content.task.question.input.edit', ["id" => $taskId, "questionId" => $questionId, "inputId" => $inputId])
      ->with("saveOk", "Input field updated successfully");
  }

  /**
   * POST /admin/content/task/{id}/question/{questionId}/input/{inputId}/delete
   * Handle the POST back to delete an input field on our question
   */
  public function question_input_delete(Request $request, $taskId, $questionId, $inputId) {
    AuditLog::Log("Content.Task($taskId).Question($questionId).Input($inputId).Delete", $request);    
    $inputField = InputField::findOrFail($inputId);
    $inputField->delete();   
    
    return Redirect::route('admin.content.task.question.inputs', ["id" => $taskId, "questionId" => $questionId])
      ->with("saveOk", "Input field deleted successfully");
  }

  /**************************************************************************************************
   * DEAL WITH CHECKBOX OPTIONS ON INPUT FIELD
   * 
   */
  public function question_input_checkbox_add(Request $request, $taskId, $questionId, $inputId) { 
    $option = new InputOption();
    $option->input_field_id = $inputId;
    $option->label = "New Option";
    $option->value = "New Option";
    $option->risks = "{}";
    $option->sort_order = 999;
    $option->save();

    return Redirect::route('admin.content.task.question.input.edit', 
      ["id" => $taskId, "questionId" => $questionId, "inputId" => $inputId]);
  }

  /**
   * GET /admin/content/task/{id}/question/{questionId}/inputs/{inputId}/checkbox/{optionId}/edit
   */
  public function question_input_checkbox_edit(Request $request, $taskId, $questionId, $inputId, $optionId) {     
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::with('inputFields')->findOrFail($questionId);
    $inputField = InputField::with("input_options")->findOrFail($inputId);    
    $risks = Risk::all();    
    $option = InputOption::findOrFail($optionId);

    return Inertia::render('Admin/Content/Tasks/Questions/Input.Checkbox.Edit', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'question' => $question,
      'input' => $inputField,
      'option' => $option,
      'risks' => $risks,
      'thresholds' => ImpactThreshold::all(),
    ]);
  }
  
  /**
   * POST /admin/content/task/{taskId}/question/{questionId}/inputs/{inputId}/checkbox/{optionId}/save
   */
  public function question_input_checkbox_save(Request $request, $taskId, $questionId, $inputId, $optionId) {     
    $option = InputOption::findOrFail($optionId);
    $option->validateAnswers($request->all());

    return back()->withInput()->withErrors($option->errors);
  }

  /**
   * POST /admin/content/task/{id}/question/{questionId}/inputs/{inputId}/checkbox/delete
   * The request parameter is the Id of the checkbox option
   */
  public function question_input_checkbox_delete(Request $request, $taskId, $questionId, $inputId, $optionId) { 
    AuditLog::Log("Content.Task($taskId).Question($questionId).Input($inputId).InputOption.Delete", $request);    
    $option = InputOption::findOrFail($optionId);
    $option->delete();
    return Redirect::route('admin.content.task.question.input.edit', ["id" => $taskId, "questionId" => $questionId, "inputId" => $inputId]);
  }

  /**************************************************
   * RISK STUFF
   */

  /**
   * GET /admin/content/task/{taskId}/risks
   * Load the list of risks for this task
   */
  public function risks(Request $request, $taskId) {
    $task = Task::findOrFail($taskId);
    $questionnaire = Questionnaire::findOrFail($task->task_object_id);
    $risks = QuestionnaireRisk::where(['questionnaire_id' => $questionnaire->id])->get();
  
    return Inertia::render('Admin/Content/Tasks/Task.Risks', [
      'siteConfig' => Configuration::site_config(),
      'task' => $task,
      'questionnaire' => $questionnaire,
      'risks' => $risks,
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * POST /admin/content/task/{taskId}/risk/create
   */
  public function risk_create(Request $request, $taskId) {
    AuditLog::Log("Admin.Content.Task($taskId).Risk.Create", $request);
    $task = Task::findOrFail($taskId);
    $questionnaire = Questionnaire::findOrFail($task->task_object_id);
    $riskName = $request->input('name', '');
    $riskDescription = $request->input('description', '');
    Log::Info("Adding $riskName to Task $taskId");

    $risk = QuestionnaireRisk::firstOrNew(["name" => $riskName]);
    $risk->description = $riskDescription;
    $risk->questionnaire_id = $questionnaire->id;
    $risk->save();
    
    return Redirect::route('admin.content.task.risks', ["id" => $taskId])
      ->with('saveOk', 'Risk has been created successfully on task');
  }

  /**
   * POST /admin/content/task/{taskId}/risk/{riskId}/delete
   */
  public function risk_delete(Request $request, $taskId, $riskId) {
    AuditLog::Log("Admin.Content.Task($taskId).Risk($riskId).Delete", $request);
    $risk = QuestionnaireRisk::findOrFail($riskId);
    $risk->delete();

    return Redirect::route('admin.content.task.risks', ["id" => $taskId])
      ->with('saveOk', 'Risk has been deleted successfully from task');
  }
}