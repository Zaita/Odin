<?php

use App\Http\Controllers\Admin\Home\AuditLogController as Admin_Home_AuditLog;
use App\Http\Controllers\Admin\Home\ReportController as Admin_Home_Report;
use App\Http\Controllers\Admin\Content\DashboardController as Admin_Content_Dashboard;
use App\Http\Controllers\Admin\Content\PillarController as Admin_Content_Pillar;
use App\Http\Controllers\Admin\Content\TaskController as Admin_Content_Task;
use App\Http\Controllers\Admin\Content\SecurityCatalogueController as Admin_Content_SecurityCatalogue;
use App\Http\Controllers\Admin\Security\GroupController as Admin_Security_Group;
use App\Http\Controllers\Admin\Security\UserController as Admin_Security_User;
use App\Http\Controllers\Admin\Records\SubmissionsController as Admin_Records_Submissions;
use App\Http\Controllers\Admin\Configuration\SettingsController as Admin_Configuration_Settings;
use App\Http\Controllers\Admin\Configuration\EmailController as Admin_Configuration_Email;
use App\Http\Controllers\Admin\Configuration\RiskController as Admin_Configuration_Risks;
use App\Http\Controllers\Admin\Configuration\HelpController as Admin_Configuration_Help;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\OAuth2Controller;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('oauth2/okta', [OAuth2Controller::class, 'redirectToIdp'])->name('login.okta');
Route::get('oauth2/okta/callback', [OAuth2Controller::class, 'handleIdpCallback']);

Route::get('/landing', function () {
    return Inertia::render('landing');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
User Routes
*/
Route::middleware('auth')->group(function () {
  Route::get('/', [HomeController::class, 'index'])->name('home');
  Route::get('/submissions', [HomeController::class, 'submissions'])->name('submissions');
  Route::get('/approvals', [HomeController::class, 'approvals'])->name('approvals');
  Route::get('/help', [HomeController::class, 'help'])->name('help');
  Route::get('/controls', [HomeController::class, 'securityControls'])->name('controls');
  Route::get('/control/{id}', [HomeController::class, 'securityControl_view'])->name('control.view');
  Route::get('/error', [HomeController::class, 'error'])->name('error');
  
  Route::get('/start/{pillarId}', [SubmissionController::class, 'information'])->name('submission.information');
  Route::post('/start/{pillarId}', [SubmissionController::class, 'start'])->name('submission.start');
  Route::get('/view/{pillarId}', [SubmissionController::class, 'view'])->name('submission.view');
  Route::get('/inprogress/{uuid}', [SubmissionController::class, 'inProgress'])->name('submission.inprogress');
  Route::post('/inprogress/{uuid}', [SubmissionController::class, 'update'])->name('submission.update');
  Route::get('/review/{uuid}', [SubmissionController::class, 'review'])->name('submission.review');
  Route::post('/submit/{uuid}', [SubmissionController::class, 'submit'])->name('submission.submit');
  Route::get('/submitted/{uuid}', [SubmissionController::class, 'submitted'])->name('submission.submitted');
  Route::get('/viewanswers/{uuid}', [SubmissionController::class, 'viewAnswers'])->name('submission.viewanswers');
  Route::get('/edit/{uuid}', [SubmissionController::class, 'edit'])->name('submission.edit');
  Route::post('/submitforapproval/{uuid}', [SubmissionController::class, 'submitForApproval'])->name('submission.submitforapproval');
  Route::post('/assigntome/{uuid}', [SubmissionController::class, 'assignToMe'])->name('submission.assigntome');
  Route::post('/downloadpdf/{uuid}', [SubmissionController::class, 'submitted'])->name('submission.downloadpdf');
  Route::post('/submission/{uuid}/collaborators/add', [SubmissionController::class, 'addCollaborator'])->name('submission.collaborator.add');

  Route::post('/sendback/{uuid}', [SubmissionController::class, 'sendBackForChanges'])->name('submission.sendback');
  Route::post('/deny/{uuid}', [SubmissionController::class, 'deny'])->name('submission.deny');
  Route::post('/approve/{uuid}', [SubmissionController::class, 'approve'])->name('submission.approve');

  Route::get('/task/{uuid}', [SubmissionController::class, 'task_index'])->name('submission.task');
  Route::post('/task/start/{uuid}', [SubmissionController::class, 'task_start'])->name('submission.task.start');
  Route::get('/task/inprogress/{uuid}', [SubmissionController::class, 'task_inprogress'])->name('submission.task.inprogress');
  Route::post('/task/inprogress/{uuid}', [SubmissionController::class, 'task_update'])->name('submission.task.update');
  Route::get('/task/review/{uuid}', [SubmissionController::class, 'task_review'])->name('submission.task.review');
  Route::post('/task/submit/{uuid}', [SubmissionController::class, 'task_submit'])->name('submission.task.submit');
  Route::get('/task/submitted/{uuid}', [SubmissionController::class, 'task_submitted'])->name('submission.task.submitted');
  Route::get('/task/view/{uuid}', [SubmissionController::class, 'task_view'])->name('submission.task.view');
  // DSRA specific routes
  Route::post('/dsra/control/update/{id}', [SubmissionController::class, 'dsra_control_update'])->name('submission.dsra.control.update');
});


/**
 * ADMIN PANEL ROUTES.
 * These have different role based access control that is managed through middleware layers
 * Groups are:
 * - Administrator
 * - Read Only Administrator
 * - Content Administrator
 * - Audit Log Viewer
 * - Report Viewer
 */

/**
 * Check for any form of administrator access
 */
Route::middleware(['auth', 'can:isAnyAdmin'])->group(function() {
  Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');
});

/**
 * Report Viewer admin role can view and execute reports
 */
Route::middleware(['auth', 'can:isReportViewer'])->group(function() {
  // Home -> Reports
  Route::get('/admin/home/reports', [Admin_Home_Report::class, 'index'])->name('admin.home.reports');
  Route::get('/admin/home/report/{id}', [Admin_Home_Report::class, 'execute'])->name('admin.home.report.execute');
});

/**
 * Audit Log Viewer can view audit logs
 */
Route::middleware(['auth', 'can:isAuditLogViewer'])->group(function() {
  // Home -> Audit Log
  Route::get('/admin/home/auditlog', [Admin_Home_AuditLog::class, 'index'])->name('admin.home.auditlog');
  Route::get('/admin/home/auditlog/{auditId}', [Admin_Home_AuditLog::class, 'view'])->name('admin.home.auditlog.view');
});

/**
 * Read Only Administrator Can view most of the admin panel
 */
Route::middleware(['auth', 'can:isReadOnlyAdministrator'])->group(function() {
  // Security -> Users
  Route::get('/admin/security', [AdminController::class, 'index'])->name('admin.security');
  Route::get('/admin/security/users', [Admin_Security_User::class, 'index'])->name('admin.security.users');
  // Security -> Groups
  Route::get('/admin/security/groups', [Admin_Security_Group::class, 'index'])->name('admin.security.groups');
  // Content -> Dashboard
  Route::get('/admin/content/dashboard', [Admin_Content_Dashboard::class, 'index'])->name('admin.content.dashboard');
  Route::get('/admin/content/dashboard/pillars', [Admin_Content_Dashboard::class, 'pillars'])->name('admin.content.dashboard.pillars');
  Route::get('/admin/content/dashboard/tasks', [Admin_Content_Dashboard::class, 'tasks'])->name('admin.content.dashboard.tasks');
  // Content -> Pillars
  Route::get('/admin/content/pillars', [Admin_Content_Pillar::class, 'index'])->name('admin.content.pillars');
  Route::get('/admin/content/pillars/edit/{id}', [Admin_Content_Pillar::class, 'edit'])->name('admin.content.pillar.edit');
  Route::get('/admin/content/pillar/download/{id}', [Admin_Content_Pillar::class, 'download'])->name('admin.content.pillar.download');   
  // Content -> Pillars -> Pillar
  Route::get('/admin/content/pillars/{id}/questions', [Admin_Content_Pillar::class, 'pillar_questions'])->name('admin.content.pillar.questions');
  Route::get('/admin/content/pillars/{id}/tasks', [Admin_Content_Pillar::class, 'pillar_tasks'])->name('admin.content.pillar.tasks');
  Route::get('/admin/content/pillars/{id}/risks', [Admin_Content_Pillar::class, 'pillar_risks'])->name('admin.content.pillar.risks');
  // Submissions -> Overview
  Route::get('/admin/records/submissions', [Admin_Records_Submissions::class, 'index'])->name('admin.records.submissions');
  Route::get('/admin/records/submission/{id}', [Admin_Records_Submissions::class, 'view'])->name('admin.records.submission.view');
  Route::get('/admin/records/submissions/download', [Admin_Records_Submissions::class, 'index'])->name('admin.records.submission.download');
});

/**
 * Content Administrator. Can modify files
 */
Route::middleware(['auth', 'can:isContentAdministrator'])->group(function() {
  // Content -> Dashboard
  Route::post('/admin/content/dashboard', [Admin_Content_Dashboard::class, 'update'])->name('admin.content.dashboard.update'); 
  Route::post('/admin/content/dashboard/pillars', [Admin_Content_Dashboard::class, 'updatePillarOrder'])->name('admin.content.dashboard.pillars.updateorder');
  // Content -> Pillars  
  Route::get('/admin/content/pillars/add', [Admin_Content_Pillar::class, 'add'])->name('admin.content.pillars.add');
  Route::post('/admin/content/pillar/add', [Admin_Content_Pillar::class, 'create'])->name('admin.content.pillar.create');
  Route::post('/admin/content/pillar/{id}/save', [Admin_Content_Pillar::class, 'save'])->name('admin.content.pillar.save');   
  Route::post('/admin/content/pillar/{id}/delete', [Admin_Content_Pillar::class, 'delete'])->name('admin.content.pillars.delete');   
  // Content -> Pillars -> Pillar  
  Route::get('/admin/content/pillar/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_add'])->name('admin.content.pillar.question.add');
  Route::post('/admin/content/pillar/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_create'])->name('admin.content.pillar.question.create');
  Route::post('/admin/content/pillar/{id}/questions/reorder', [Admin_Content_Pillar::class, 'pillar_questions_reorder'])->name('admin.content.pillar.questions.reorder');
  // Content -> Pillars -> Pillar -> Questions
  Route::post('/admin/content/pillar/{id}/questions/{questionId}/delete', [Admin_Content_Pillar::class, 'pillar_question_delete'])->name('admin.content.pillar.question.delete');
  Route::get('/admin/content/pillar/{id}/questions/{questionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_edit'])->name('admin.content.pillar.question.edit');
  Route::post('/admin/content/pillar/{id}/questions/{questionId}/save', [Admin_Content_Pillar::class, 'pillar_question_save'])->name('admin.content.pillar.question.save');
  // Content -> Pillars -> Pillar -> Questions -> Question -> Inputs
  Route::get('/admin/content/pillar/{id}/question/{questionId}/inputs', [Admin_Content_Pillar::class, 'pillar_question_inputs'])->name('admin.content.pillar.question.inputs');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/inputs/reorder', [Admin_Content_Pillar::class, 'pillar_question_inputs_reorder'])->name('admin.content.pillar.question.inputs.reorder');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/input/add', [Admin_Content_Pillar::class, 'pillar_question_input_add'])->name('admin.content.pillar.question.input.add');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/input/create', [Admin_Content_Pillar::class, 'pillar_question_input_create'])->name('admin.content.pillar.question.input.create');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/edit', [Admin_Content_Pillar::class, 'pillar_question_input_edit'])->name('admin.content.pillar.question.input.edit');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/save', [Admin_Content_Pillar::class, 'pillar_question_input_save'])->name('admin.content.pillar.question.input.save');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/delete', [Admin_Content_Pillar::class, 'pillar_question_input_delete'])->name('admin.content.pillar.question.input.delete');
  // Content -> Pillars -> Pillar -> Questions -> Question -> Inputs -> Checkbox(type)
  Route::get('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/checkbox/add', [Admin_Content_Pillar::class, 'pillar_question_input_checkbox_add'])->name('admin.content.pillar.question.input.checkbox.add');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_input_checkbox_edit'])->name('admin.content.pillar.question.input.checkbox.edit');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/save', [Admin_Content_Pillar::class, 'pillar_question_input_checkbox_save'])->name('admin.content.pillar.question.input.checkbox.save');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/delete', [Admin_Content_Pillar::class, 'pillar_question_input_checkbox_delete'])->name('admin.content.pillar.question.input.checkbox.delete');

  // Content -> Pillars -> Pillar -> Questions -> Question -> Actions
  Route::get('/admin/content/pillar/{id}/question/{questionId}/actions', [Admin_Content_Pillar::class, 'pillar_question_actions'])->name('admin.content.pillar.question.actions');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/actions/reorder', [Admin_Content_Pillar::class, 'pillar_question_actions_reorder'])->name('admin.content.pillar.question.actions.reorder');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/action/add', [Admin_Content_Pillar::class, 'pillar_question_action_add'])->name('admin.content.pillar.question.action.add');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/action/create', [Admin_Content_Pillar::class, 'pillar_question_action_create'])->name('admin.content.pillar.question.action.create');
  Route::get('/admin/content/pillar/{id}/question/{questionId}/action/{actionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_action_edit'])->name('admin.content.pillar.question.action.edit');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/action/{actionId}/save}', [Admin_Content_Pillar::class, 'pillar_question_action_save'])->name('admin.content.pillar.question.action.save');
  Route::post('/admin/content/pillar/{id}/question/{questionId}/action/{actionId}/delete}', [Admin_Content_Pillar::class, 'pillar_question_action_delete'])->name('admin.content.pillar.question.action.delete');

  // Content -> Pillars -> Pillar -> Tasks
  Route::post('/admin/content/pillar/{id}/tasks/link', [Admin_Content_Pillar::class, 'pillar_task_link'])->name('admin.content.pillar.task.link');
  Route::post('/admin/content/pillar/{id}/task/{taskId}/unlink', [Admin_Content_Pillar::class, 'pillar_task_unlink'])->name('admin.content.pillar.task.unlink');
 
  // Content -> Pillars -> Pillar -> Risks
  Route::post('/admin/content/pillar/{id}/risk/create', [Admin_Content_Pillar::class, 'pillar_risk_create'])->name('admin.content.pillar.risk.create');
  Route::post('/admin/content/pillar/{id}/risk/{riskId}/delete', [Admin_Content_Pillar::class, 'pillar_risk_delete'])->name('admin.content.pillar.risk.delete');

  // Content -> Tasks
  Route::get('/admin/content/tasks', [Admin_Content_Task::class, 'index'])->name('admin.content.tasks');
  Route::get('/admin/content/task/add', [Admin_Content_Task::class, 'add'])->name('admin.content.task.add');
  Route::post('/admin/content/task/add', [Admin_Content_Task::class, 'create'])->name('admin.content.task.create');
  Route::get('/admin/content/task/edit/{id}', [Admin_Content_Task::class, 'edit'])->name('admin.content.task.edit');
  Route::post('/admin/content/task/save/{id}', [Admin_Content_Task::class, 'save'])->name('admin.content.task.save');
  Route::post('/admin/content/task/dsrasave/{id}', [Admin_Content_Task::class, 'dsra_save'])->name('admin.content.task.dsrasave');
  Route::post('/admin/content/task/delete', [Admin_Content_Task::class, 'delete'])->name('admin.content.task.delete');   
  Route::get('/admin/content/task/download/{id}', [Admin_Content_Task::class, 'download'])->name('admin.content.task.download');   
  // Content -> Task -> Questions
  Route::get('/admin/content/task/{id}/questions', [Admin_Content_Task::class, 'questions'])->name('admin.content.task.questions');
  Route::get('/admin/content/task/{id}/question/add', [Admin_Content_Task::class, 'question_add'])->name('admin.content.task.question.add');
  Route::post('/admin/content/task/{id}/question/add', [Admin_Content_Task::class, 'question_create'])->name('admin.content.task.question.create');
  Route::post('/admin/content/task/{id}/questions/reorder', [Admin_Content_Task::class, 'questions_reorder'])->name('admin.content.task.questions.reorder');
  // Content -> Tasks -> Questions -> Question
  Route::post('/admin/content/task/{id}/question/{questionId}/delete', [Admin_Content_Task::class, 'question_delete'])->name('admin.content.task.question.delete');
  Route::get('/admin/content/task/{id}/question/{questionId}/edit', [Admin_Content_Task::class, 'question_edit'])->name('admin.content.task.question.edit');
  Route::post('/admin/content/task/{id}/question/{questionId}/save', [Admin_Content_Task::class, 'question_save'])->name('admin.content.task.question.save');
  // Content -> Tasks -> Questions -> Question -> Inputs
  Route::get('/admin/content/task/{id}/question/{questionId}/inputs', [Admin_Content_Task::class, 'question_inputs'])->name('admin.content.task.question.inputs');
  Route::post('/admin/content/task/{id}/question/{questionId}/inputs/reorder', [Admin_Content_Task::class, 'question_inputs_reorder'])->name('admin.content.task.question.inputs.reorder');
  Route::get('/admin/content/task/{id}/question/{questionId}/input/add', [Admin_Content_Task::class, 'question_input_add'])->name('admin.content.task.question.input.add');
  Route::post('/admin/content/task/{id}/question/{questionId}/input/create', [Admin_Content_Task::class, 'question_input_create'])->name('admin.content.task.question.input.create');
  Route::get('/admin/content/task/{id}/question/{questionId}/input/{inputId}/edit', [Admin_Content_Task::class, 'question_input_edit'])->name('admin.content.task.question.input.edit');
  Route::post('/admin/content/task/{id}/question/{questionId}/input/{inputId}/save', [Admin_Content_Task::class, 'question_input_save'])->name('admin.content.task.question.input.save');
  Route::post('/admin/content/task/{id}/question/{questionId}/input/{inputId}/delete', [Admin_Content_Task::class, 'question_input_delete'])->name('admin.content.task.question.input.delete');
  // Content -> Tasks -> Questions -> Question -> Inputs -> Checkbox  
  Route::get('/admin/content/task/{id}/question/{questionId}/input/{inputId}/checkbox/add', [Admin_Content_Task::class, 'question_input_checkbox_add'])->name('admin.content.task.question.input.checkbox.add');
  Route::get('/admin/content/task/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/edit', [Admin_Content_Task::class, 'question_input_checkbox_edit'])->name('admin.content.task.question.input.checkbox.edit');
  Route::post('/admin/content/task/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/save', [Admin_Content_Task::class, 'question_input_checkbox_save'])->name('admin.content.task.question.input.checkbox.save');
  Route::post('/admin/content/task/{id}/question/{questionId}/input/{inputId}/checkbox/{optionId}/delete', [Admin_Content_Task::class, 'question_input_checkbox_delete'])->name('admin.content.task.question.input.checkbox.delete');
  // Content -> Tasks -> Questions -> Question -> Actions
  Route::get('/admin/content/task/{id}/question/{questionId}/actions', [Admin_Content_Task::class, 'question_actions'])->name('admin.content.task.question.actions');
  // Content -> Tasks -> Risks
  Route::get('/admin/content/task/{id}/risks', [Admin_Content_Task::class, 'risks'])->name('admin.content.task.risks');
  Route::post('/admin/content/task/{id}/risk/create', [Admin_Content_Task::class, 'risk_create'])->name('admin.content.task.risk.create');
  Route::post('/admin/content/task/{id}/risk/{riskId}/delete', [Admin_Content_Task::class, 'risk_delete'])->name('admin.content.task.risk.delete');  
  // Content -> Tasks (DSRA) -> Likelihoods
  Route::get('/admin/content/task/{id}/likelihoods', [Admin_Content_Task::class, 'risks'])->name('admin.content.task.likelihoods');
  // Content -> Tasks (DSRA) -> Impacts
  Route::get('/admin/content/task/{id}/impacts', [Admin_Content_Task::class, 'risks'])->name('admin.content.task.impacts');
  // Content -> Tasks (DSRA) -> RiskMatrix
  Route::get('/admin/content/task/{id}/riskmatrix', [Admin_Content_Task::class, 'risks'])->name('admin.content.task.riskmatrix');
  
  // Content -> Security Catalogues
  Route::get('/admin/content/securitycatalogues', [Admin_Content_SecurityCatalogue::class, 'index'])->name('admin.content.securitycatalogues');
  Route::get('/admin/content/securitycatalogues/add', [Admin_Content_SecurityCatalogue::class, 'add'])->name('admin.content.securitycatalogue.add');  
  Route::post('/admin/content/securitycatalogues/add', [Admin_Content_SecurityCatalogue::class, 'create'])->name('admin.content.securitycatalogue.create');
  Route::get('/admin/content/securitycatalogues/{id}/edit', [Admin_Content_SecurityCatalogue::class, 'edit'])->name('admin.content.securitycatalogue.edit');
  Route::post('/admin/content/securitycatalogues/{id}/save', [Admin_Content_SecurityCatalogue::class, 'save'])->name('admin.content.securitycatalogue.save');
  Route::post('/admin/content/securitycatalogues/{id}/delete', [Admin_Content_SecurityCatalogue::class, 'delete'])->name('admin.content.securitycatalogue.delete');
  Route::get('/admin/content/securitycatalogues/{id}/download', [Admin_Content_SecurityCatalogue::class, 'download'])->name('admin.content.securitycatalogue.download');  
  Route::get('/admin/content/securitycatalogues/{id}/controls', [Admin_Content_SecurityCatalogue::class, 'controls'])->name('admin.content.securitycatalogue.controls');
  // Content -> Security Catalogues -> Controls
  Route::get('/admin/content/securitycatalogues/{id}/control/add', [Admin_Content_SecurityCatalogue::class, 'control_add'])->name('admin.content.securitycontrol.add');  
  Route::post('/admin/content/securitycatalogues/{id}/control/add', [Admin_Content_SecurityCatalogue::class, 'control_create'])->name('admin.content.securitycontrol.create');
  Route::get('/admin/content/securitycatalogues/{id}/control/{controlId}/edit', [Admin_Content_SecurityCatalogue::class, 'control_edit'])->name('admin.content.securitycontrol.edit');
  Route::post('/admin/content/securitycatalogue/{id}/control/{controlId}/save', [Admin_Content_SecurityCatalogue::class, 'control_save'])->name('admin.content.securitycontrol.save');
  Route::post('/admin/content/securitycatalogues/{id}/control/{controlId}/delete', [Admin_Content_SecurityCatalogue::class, 'control_delete'])->name('admin.content.securitycontrol.delete');
  Route::get('/admin/content/securitycatalogues/control/{controlId}/download', [Admin_Content_SecurityCatalogue::class, 'control_save'])->name('admin.content.securitycontrol.download');  

  // Service Inventory -> Accreditations
  Route::get('/admin/services', [AdminController::class, 'index'])->name('admin.services');
  Route::get('/admin/services/accreditations', [AdminController::class, 'index'])->name('admin.services.accreditations');
});

/**
 * Full administrator rights to modify stuff
 */
Route::middleware(['auth', 'can:isAdministrator'])->group(function() {
  // Security -> Users  
  Route::post('/admin/security/user/delete', [Admin_Security_User::class, 'delete'])->name('admin.security.user.delete');
  Route::get('/admin/security/user/add', [Admin_Security_User::class, 'add'])->name('admin.security.user.add');  
  Route::post('/admin/security/user/add', [Admin_Security_User::class, 'create'])->name('admin.security.user.create');
  Route::get('/admin/security/user/{id}/edit', [Admin_Security_User::class, 'edit'])->name('admin.security.user.edit');  
  Route::post('/admin/security/user/{id}/save', [Admin_Security_User::class, 'save'])->name('admin.security.user.save');  
  Route::post('/admin/security/users/groupadd', [Admin_Security_User::class, 'addToGroup'])->name('admin.security.user.groups.add');  
  
  // Security -> Groups
  Route::post('/admin/security/groups/delete', [Admin_Security_Group::class, 'delete'])->name('admin.security.groups.delete');
  Route::get('/admin/security/groups/add', [Admin_Security_Group::class, 'add'])->name('admin.security.groups.add');  
  Route::post('/admin/security/groups/add', [Admin_Security_Group::class, 'create'])->name('admin.security.groups.create');
  Route::get('/admin/security/groups/edit/{id}', [Admin_Security_Group::class, 'edit'])->name('admin.security.groups.edit');  
  Route::post('/admin/security/groups/save', [Admin_Security_Group::class, 'save'])->name('admin.security.groups.save');  
  // Configuration -> Settings
  Route::get('/admin/configuration/settings', [Admin_Configuration_Settings::class, 'index'])->name('admin.configuration.settings');
  Route::post('/admin/configuration/settings', [Admin_Configuration_Settings::class, 'save'])->name('admin.configuration.settings.save');
  Route::get('/admin/configuration/settings/theme', [Admin_Configuration_Settings::class, 'theme'])->name('admin.configuration.settings.theme');
  Route::post('/admin/configuration/settings/theme', [Admin_Configuration_Settings::class, 'theme_save'])->name('admin.configuration.settings.theme.save');
  Route::get('/admin/configuration/settings/images', [Admin_Configuration_Settings::class, 'theme'])->name('admin.configuration.settings.images');
  Route::get('/admin/configuration/settings/alert', [Admin_Configuration_Settings::class, 'theme'])->name('admin.configuration.settings.alert');
  // Configuration -> Email
  Route::get('/admin/configuration/email', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email');
  Route::get('/admin/configuration/email/start', [Admin_Configuration_Email::class, 'start'])->name('admin.configuration.email.start');
  Route::post('/admin/configuration/email/start', [Admin_Configuration_Email::class, 'start'])->name('admin.configuration.email.start.save');
  Route::get('/admin/configuration/email/summary', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email.summary');
  Route::get('/admin/configuration/email/submitted', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email.submitted');
  Route::get('/admin/configuration/email/alltaskscomplete', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email.alltaskscomplete');
  Route::get('/admin/configuration/email/approval', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email.approval');
  Route::get('/admin/configuration/email/tasks', [Admin_Configuration_Email::class, 'index'])->name('admin.configuration.email.tasks');
  // Configuration -> Risks
  Route::get('/admin/configuration/risks', [Admin_Configuration_Risks::class, 'index'])->name('admin.configuration.risks');
  Route::get('/admin/configuration/risks/add', [Admin_Configuration_Risks::class, 'add'])->name('admin.configuration.risk.add');
  Route::post('/admin/configuration/risks/add', [Admin_Configuration_Risks::class, 'create'])->name('admin.configuration.risk.create');
  Route::get('/admin/configuration/risks/edit/{id}', [Admin_Configuration_Risks::class, 'edit'])->name('admin.configuration.risk.edit');
  Route::post('/admin/configuration/risks/save/{id}', [Admin_Configuration_Risks::class, 'save'])->name('admin.configuration.risk.save');
  Route::post('/admin/configuration/risks/delete', [Admin_Configuration_Risks::class, 'delete'])->name('admin.configuration.risk.delete');   
  // Configuration -> Help
  Route::get('/admin/configuration/help', [Admin_Configuration_Help::class, 'index'])->name('admin.configuration.help');
  // Configuration -> Single Sign-On  
  Route::get('/admin/configuration/sso', [AdminController::class, 'index'])->name('admin.configuration.sso');
});

require __DIR__.'/auth.php';

