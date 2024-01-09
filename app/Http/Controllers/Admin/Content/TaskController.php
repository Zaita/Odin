<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Task;
use App\Models\Group;
use App\Http\Requests\TaskRequest;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Http\Requests\AdminContentPillarUpdateRequest;
use App\Http\Requests\InputFieldRequest;
use App\Http\Requests\ActionFieldRequest;
use App\Http\Requests\AdminSiteConfigUpdateRequest;
use App\Http\Requests\QuestionnaireQuestionRequest;

class TaskController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $tasks = Task::orderBy('created_at')->paginate(20);
    
    return Inertia::render('Admin/Content/Tasks', [
      'siteConfig' => $config,
      'tasks' => $tasks
    ]); 
  }

  /**
   * Content -> Tasks -> Add
   * Load the add screen
   */
  public function add(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Content/Tasks/Add', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * Handle POST from Content -> Tasks -> Add
   * Create a new task
   */
  public function create(TaskRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Tasks.Add", $request);  
    
    $obj = Task::firstOrNew(["name" => $request->safe()->only("name")]);
    if (!is_null($obj->id)) {
      return back()->withInput()->withErrors(array("name" => "Name already exists, must be unique across all tasks"));
    }

    $obj->fill($request->validated());
    $obj->save();

    return Redirect::route('admin.content.tasks');
  }

  /**
   * Content -> Tasks -> Edit
   * Load edit screen for a task
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Content.Task.Edit", $request);
    $task = Task::findOrFail($id);     
    
    if ($task->type == "questionnaire") {
      $groups = Group::all();
      return Inertia::render('Admin/Content/Tasks/Questionnaire/Edit', [
        'siteConfig' => json_decode(Configuration::GetSiteConfig()->value),
        'task' => $task,
        'groups' => $groups,
      ]); 
    
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

    /**
   * Content -> Task -> Edit -> Questions
   * Load questions for a task
   */
  public function questions(Request $request, $id) {
    AuditLog::Log("Content.Task.Edit", $request);
    $task = Task::findOrFail($id);
      
    if ($task->type == "questionnaire") {
      $task->questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      return Inertia::render('Admin/Content/Tasks/Questionnaire/Questions', [
        'siteConfig' => json_decode(Configuration::GetSiteConfig()->value),
        'task' => $task,
      ]); 
    
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * GET Content -> Task -> Edit -> Questions -> Add
   */
  public function question_add(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($id);
    
    if ($task->type == "questionnaire") {
      $task->questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      return Inertia::render('Admin/Content/Tasks/Questionnaire/Questions/Add', [
        'siteConfig' => json_decode(Configuration::GetSiteConfig()->value),
        'task' => $task,
      ]); 
    
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * POST Content -> Task -> Edit -> Question -> Save/Create
   */
  public function question_create(QuestionnaireQuestionRequest $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($id);

    if ($task->type == "questionnaire") {
      $questionnaire = Questionnaire::with(["questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($task->task_object_id);
      
      $newQuestion = new QuestionnaireQuestion($request->validated());
      $newQuestion->sort_order = count($questionnaire->questions);
      $questionnaire->questions()->save($newQuestion);

      return Redirect::route('admin.content.task.questions', $task->id);    
    } else {
      return Redirect::route('admin.content.tasks');
    }
  }

  /**
   * Update the order of our questions in the pillar
   */
  public function questions_reorder(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($id);
    
    if ($task->type == "questionnaire") {
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
   * Delete a question
   */
  public function question_delete(Request $request, $taskId, $questionId) {
    AuditLog::Log("Content.Task(${taskId}).Question(${questionId}).Delete", $request);
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->delete();    
    return Redirect::route('admin.content.task.questions', $task->id);
  }

  /**
   * Load the edit screen for adding a new question to a pillar
   */
  public function question_edit(Request $request, $taskId, $questionId) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Tasks/Questionnaire/Questions/Edit', [
      'siteConfig' => $config,
      'task' => $task,
      'question' => $question,
    ]); 
  }

  /**
   * Save changes to a question 
   */
  public function question_save(QuestionnaireQuestionRequest $request, $taskId, $questionId) {
    AuditLog::Log("Content.Task(${taskId}).Question(${questionId}).Update", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->update($request->validated());
    $question->save();
    return Redirect::route('admin.content.task.question.edit', ["id" => $taskId, "questionId" => $questionId]);
  }

  /**
   * Load our "Inputs" screen for a question
   */
  public function question_inputs(Request $request, $taskId, $questionId) {
    AuditLog::Log("Content.Task(${taskId}).Question(${questionId}).Inputs", $request);
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $task = Task::findOrFail($taskId);
    $question = QuestionnaireQuestion::with(['inputFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Tasks/Questionnaire/Questions/Inputs/View', [
      'siteConfig' => $config,
      'task' => $task,
      'question' => $question,
    ]); 
  }

  /**
   * Update the order of the input fields for our question
   */
  public function question_inputs_reorder(Request $request, $taskId, $questionId) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
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
}