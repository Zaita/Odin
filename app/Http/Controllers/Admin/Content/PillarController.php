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
use App\Models\InputField;
use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use App\Http\Requests\AdminContentPillarUpdateRequest;
use App\Http\Requests\InputFieldRequest;
use App\Http\Requests\AdminSiteConfigUpdateRequest;
use App\Http\Requests\QuestionnaireQuestionRequest;

class PillarController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillars = Pillar::orderBy('created_at')->paginate(20);
    
    return Inertia::render('Admin/Content/Pillars', [
      'siteConfig' => $config,
      'pillars' => $pillars
    ]); 
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
   * Show the add page
   */
  public function add(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Content/Pillars/Add', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * Create a new pillar
   */
  public function create(AdminContentPillarUpdateRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Pillars.Add", $request);    
    $q = Questionnaire::create($request->safe()->only(['name', 'type']));
    $p = new Pillar();
    $p->fill($request->safe()->except("type"));
    $p->questionnaire_id = $q->id;
    $p->save();
    return Redirect::route('admin.content.pillar.edit', $p->id);
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
    $pillar = Pillar::findOrFail($id); 
        
    return Inertia::render('Admin/Content/Pillars/Edit', [
      'siteConfig' => json_decode(Configuration::GetSiteConfig()->value),
      'pillar' => $pillar
    ]); 
  }

  /**
   * Save changes to our existing group
   */
  public function save(AdminContentPillarUpdateRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Pillar.Save", $request);
    $id = $request->input('id', -1);
    $pillar = Pillar::findOrFail($id); 
    $pillar->update($request->safe()->except("type"));
    
    if (!$pillar->save()) {
      return back()->withInput()->withErrors($pillar->errors);
    }
    return Redirect::route('admin.content.pillar.edit', $id);
  }

  /**
   * Load the list of questions for the pillar
   */
  public function pillar_questions_index(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::with(["questionnaire", 
      "questionnaire.questions" => function(Builder $q) {$q->orderBy('sort_order');}])->findOrFail($id);
    // "questionnaire.questions.inputFields", "questionnaire.questions.actionFields"])->findOrFail($id);
    
    return Inertia::render('Admin/Content/Pillars/Questions', [
      'siteConfig' => $config,
      'pillar' => $pillar,
    ]); 
  }

  /**
   * Load the add screen for adding a new question to a pillar
   */
  public function pillar_questions_add(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($id);
    
    return Inertia::render('Admin/Content/Pillars/Questions/Add', [
      'siteConfig' => $config,
      'pillar' => $pillar,
    ]); 
  }

  /**
   * Create the new question on the pillar based on the user
   * input supplied in the request
   */
  public function pillar_questions_create(QuestionnaireQuestionRequest $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::with(["questionnaire", "questionnaire.questions"])->findOrFail($id);

    $newQuestion = new QuestionnaireQuestion($request->validated());
    $newQuestion->sort_order = count($pillar->questionnaire->questions);
    $pillar->questionnaire->questions()->save($newQuestion);

    return Redirect::route('admin.content.pillar.questions', $pillar->id);
  }

  /**
   * Update the order of our questions in the pillar
   */
  public function pillar_questions_update(Request $request, $id) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
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
    AuditLog::Log("Content.Pillar(${pillarId}).Question(${questionId}).Delete", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->delete();    
    return Redirect::route('admin.content.pillar.questions', $pillarId);
  }

  /**
   * Load the edit screen for adding a new question to a pillar
   */
  public function pillar_question_edit(Request $request, $id, $questionId) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($id);
    $question = QuestionnaireQuestion::findorFail($questionId);
    
    return Inertia::render('Admin/Content/Pillars/Questions/Edit', [
      'siteConfig' => $config,
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }
  
  /**
   * Update the content in one of our questions. 
   */
  public function pillar_question_update(QuestionnaireQuestionRequest $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar(${pillarId}).Question(${questionId}).Update", $request);
    $question = QuestionnaireQuestion::findOrFail($questionId);
    $question->update($request->validated());
    $question->save();
    return Redirect::route('admin.content.pillar.question.edit', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Load our "Inputs" screen for a question
   */
  public function pillar_question_inputs(Request $request, $id, $questionId) {
    AuditLog::Log("Content.Pillar(${id}).Question(${questionId}).Inputs", $request);
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($id);
    $question = QuestionnaireQuestion::with(['inputFields' => function(Builder $b) { $b->orderBy("sort_order");}])->findOrFail($questionId);
    
    return Inertia::render('Admin/Content/Pillars/Questions/Inputs', [
      'siteConfig' => $config,
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }

    /**
   * Update the order of the input fields for our question
   */
  public function pillar_question_inputs_update(Request $request, $pillarId, $questionId) {
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

    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Load the Pillar->Questionnaire->Question->Inputs->Add Screen
   */
  public function pillar_question_input_add(Request $request, $pillarId, $questionId) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($pillarId);
    $question = QuestionnaireQuestion::findOrFail($questionId);

    return Inertia::render('Admin/Content/Pillars/Questions/Inputs/Add', [
      'siteConfig' => $config,
      'pillar' => $pillar,
      'question' => $question,
    ]); 
  }

  /**
   * Handle the POST back adding a new Input Field to our Question
   */
  public function pillar_question_input_create(InputFieldRequest $request, $pillarId, $questionId) {
    AuditLog::Log("Content.Pillar.Question.Input.Create", $request);    
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($pillarId);

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
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($pillarId);

    $questions = json_decode($pillar->questions, true);
    $question = $questions[$questionId];
    
    return Inertia::render('Admin/Content/Pillars/Questions/Inputs/Edit', [
      'siteConfig' => $config,
      'pillar' => $pillar,
      'question' => $question,
      'field' => $question["answerInputFields"][$inputId]
    ]); 
  }

  /**
   * Handle the POST back save an Input Field to our Question
   */
  public function pillar_question_input_save(InputFieldRequest $request, $pillarId, $questionId, $inputId) {
    AuditLog::Log("Content.Pillar.Question.Input.Save", $request);    
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillar = Pillar::findOrFail($pillarId);

    $errors = array();
    if (!$pillar->updateInputField($errors, $questionId, $inputId, $request)) {
      return back()->withInput()->withErrors($pillar->errors);  
    }

    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }

  /**
   * Handle the POST back to delete an input field on our question
   */
  public function pillar_question_input_delete(Request $request, $pillarId, $questionId, $inputId) {
    AuditLog::Log("Content.Pillar(${pillarId}).Question(${questionId}).Input(${inputId}).Delete", $request);    
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $inputField = InputField::findOrFail($inputId);
    $inputField->delete();   
    
    return Redirect::route('admin.content.pillar.question.inputs', ["id" => $pillarId, "questionId" => $questionId]);
  }
};