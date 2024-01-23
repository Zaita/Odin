<?php

use App\Http\Controllers\Admin\Home\AuditLogController as Admin_Home_AuditLog;
use App\Http\Controllers\Admin\ContentDashboardController;
use App\Http\Controllers\Admin\Content\DashboardController as Admin_Content_Dashboard;
use App\Http\Controllers\Admin\Content\PillarController as Admin_Content_Pillar;
use App\Http\Controllers\Admin\Content\TaskController as Admin_Content_Task;
use App\Http\Controllers\Admin\Security\GroupController as Admin_Security_Group;
use App\Http\Controllers\Admin\Security\UserController as Admin_Security_User;
use App\Http\Controllers\Admin\Records\SubmissionsController as Admin_Records_Submissions;
use App\Http\Controllers\Admin\Configuration\SiteSettingsController as Admin_Configuration_SiteSettings;
use App\Http\Controllers\Admin\Configuration\EmailController as Admin_Configuration_Email;
use App\Http\Controllers\Admin\Configuration\RiskController as Admin_Configuration_Risks;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AdminSiteConfigurationController;
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

Route::get('login/okta', [OAuth2Controller::class, 'redirectToIdp'])->name('login.okta');
Route::get('authorization-code/callback', [OAuth2Controller::class, 'handleIdpCallback']);

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
  Route::get('/error', [HomeController::class, 'error'])->name('error');
  
  Route::get('/start/{pillarId}', [SubmissionController::class, 'information'])->name('submission.information');
  Route::post('/start/{pillarId}', [SubmissionController::class, 'start'])->name('submission.start');
  Route::get('/view/{pillarId}', [SubmissionController::class, 'view'])->name('submission.view');
  Route::get('/inprogress/{uuid}', [SubmissionController::class, 'inProgress'])->name('submission.inprogress');
  Route::post('/inprogress/{uuid}', [SubmissionController::class, 'update'])->name('submission.update');
  Route::get('/review/{uuid}', [SubmissionController::class, 'review'])->name('submission.review');
  Route::post('/submit/{uuid}', [SubmissionController::class, 'submit'])->name('submission.submit');
  Route::get('/submitted/{uuid}', [SubmissionController::class, 'submitted'])->name('submission.submitted');
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
  });

/*
Admin Routes
*/
Route::middleware(['auth', 'can:isAdmin'])->group(function() {
  Route::get('/admin', [AdminController::class, 'index'])->name('admin.home');
  Route::get('/admin/home/reports', [AdminController::class, 'reports'])->name('admin.home.reports');
  Route::get('/admin/home/auditlog', [Admin_Home_AuditLog::class, 'index'])->name('admin.home.auditlog');
  Route::get('/admin/home/jobs', [AdminController::class, 'home'])->name('admin.home.jobs');
  
  /**
   * Security
   *  -> Users
   *  -> Groups
   */
  Route::get('/admin/security', [AdminController::class, 'security'])->name('admin.security');
  Route::get('/admin/security/users', [Admin_Security_User::class, 'index'])->name('admin.security.users');
  Route::post('/admin/security/users/delete', [Admin_Security_User::class, 'delete'])->name('admin.security.users.delete');
  Route::get('/admin/security/users/add', [Admin_Security_User::class, 'add'])->name('admin.security.users.add');  
  Route::post('/admin/security/users/add', [Admin_Security_User::class, 'create'])->name('admin.security.users.create');
  Route::get('/admin/security/users/edit/{id}', [Admin_Security_User::class, 'edit'])->name('admin.security.users.edit');  
  Route::post('/admin/security/users/groupadd', [Admin_Security_User::class, 'addToGroup'])->name('admin.security.users.groups.add');  
  Route::post('/admin/security/users/save', [Admin_Security_User::class, 'save'])->name('admin.security.users.save');  
  Route::get('/admin/security/groups', [Admin_Security_Group::class, 'index'])->name('admin.security.groups');
  Route::post('/admin/security/groups/delete', [Admin_Security_Group::class, 'delete'])->name('admin.security.groups.delete');
  Route::get('/admin/security/groups/add', [Admin_Security_Group::class, 'add'])->name('admin.security.groups.add');  
  Route::post('/admin/security/groups/add', [Admin_Security_Group::class, 'create'])->name('admin.security.groups.create');
  Route::get('/admin/security/groups/edit/{id}', [Admin_Security_Group::class, 'edit'])->name('admin.security.groups.edit');  
  Route::post('/admin/security/groups/save', [Admin_Security_Group::class, 'save'])->name('admin.security.groups.save');  
  
  /**
   * Content
   *  -> Dashboard
   *  -> Pillars
   *  -> Tasks
   *  -> Security Controls
   */
  Route::get('/admin/content/dashboard', [Admin_Content_Dashboard::class, 'index'])->name('admin.content.dashboard');
  Route::post('/admin/content/dashboard', [Admin_Content_Dashboard::class, 'update'])->name('admin.content.dashboard.update');
  Route::get('/admin/content/dashboard/pillars', [Admin_Content_Dashboard::class, 'pillars'])->name('admin.content.dashboard.pillars');
  Route::post('/admin/content/dashboard/pillars', [Admin_Content_Dashboard::class, 'updatePillarOrder'])->name('admin.content.dashboard.pillars.updateorder');
  Route::get('/admin/content/dashboard/tasks', [ContentDashboardController::class, 'tasks'])->name('admin.content.dashboard.tasks');
  // Content -> Pillars
  Route::get('/admin/content/pillars', [Admin_Content_Pillar::class, 'index'])->name('admin.content.pillars');
  Route::get('/admin/content/pillars/add', [Admin_Content_Pillar::class, 'add'])->name('admin.content.pillars.add');
  Route::post('/admin/content/pillars/add', [Admin_Content_Pillar::class, 'create'])->name('admin.content.pillar.create');
  Route::get('/admin/content/pillars/edit/{id}', [Admin_Content_Pillar::class, 'edit'])->name('admin.content.pillar.edit');
  Route::post('/admin/content/pillars/save', [Admin_Content_Pillar::class, 'save'])->name('admin.content.pillar.save');   
  Route::post('/admin/content/pillars/delete', [Admin_Content_Pillar::class, 'delete'])->name('admin.content.pillars.delete');   
  Route::get('/admin/content/pillar/download/{id}', [Admin_Content_Pillar::class, 'download'])->name('admin.content.pillar.download');   
  // Content -> Pillars -> Pillar
  Route::get('/admin/content/pillars/{id}/questions', [Admin_Content_Pillar::class, 'pillar_questions_index'])->name('admin.content.pillar.questions');
  Route::get('/admin/content/pillars/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_add'])->name('admin.content.pillar.question.add');
  Route::post('/admin/content/pillars/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_create'])->name('admin.content.pillar.question.create');
  Route::post('/admin/content/pillars/{id}/questions/reorder', [Admin_Content_Pillar::class, 'pillar_questions_reorder'])->name('admin.content.pillar.questions.reorder');
  // Content -> Pillars -> Pillar -> Questions
  Route::post('/admin/content/pillars/{id}/questions/{questionId}/delete', [Admin_Content_Pillar::class, 'pillar_question_delete'])->name('admin.content.pillar.question.delete');
  Route::get('/admin/content/pillars/{id}/questions/{questionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_edit'])->name('admin.content.pillar.question.edit');
  Route::post('/admin/content/pillars/{id}/questions/{questionId}/save', [Admin_Content_Pillar::class, 'pillar_question_save'])->name('admin.content.pillar.question.save');
  // Content -> Pillars -> Pillar -> Questions -> Question -> Inputs
  Route::get('/admin/content/pillars/{id}/question/{questionId}/inputs', [Admin_Content_Pillar::class, 'pillar_question_inputs'])->name('admin.content.pillar.question.inputs');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/inputs/reorder', [Admin_Content_Pillar::class, 'pillar_question_inputs_reorder'])->name('admin.content.pillar.question.inputs.reorder');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/input/add', [Admin_Content_Pillar::class, 'pillar_question_input_add'])->name('admin.content.pillar.question.input.add');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/create', [Admin_Content_Pillar::class, 'pillar_question_input_create'])->name('admin.content.pillar.question.input.create');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/edit', [Admin_Content_Pillar::class, 'pillar_question_input_edit'])->name('admin.content.pillar.question.input.edit');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/save', [Admin_Content_Pillar::class, 'pillar_question_input_save'])->name('admin.content.pillar.question.input.save');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/delete', [Admin_Content_Pillar::class, 'pillar_question_input_delete'])->name('admin.content.pillar.question.input.delete');
  // Content -> Pillars -> Pillar -> Questions -> Question -> Actions
  Route::get('/admin/content/pillars/{id}/question/{questionId}/actions', [Admin_Content_Pillar::class, 'pillar_question_actions'])->name('admin.content.pillar.question.actions');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/actions/reorder', [Admin_Content_Pillar::class, 'pillar_question_actions_reorder'])->name('admin.content.pillar.question.actions.reorder');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/action/add', [Admin_Content_Pillar::class, 'pillar_question_action_add'])->name('admin.content.pillar.question.action.add');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/action/create', [Admin_Content_Pillar::class, 'pillar_question_action_create'])->name('admin.content.pillar.question.action.create');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/action/{actionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_action_edit'])->name('admin.content.pillar.question.action.edit');

  // Content -> Tasks
  Route::get('/admin/content/tasks', [Admin_Content_Task::class, 'index'])->name('admin.content.tasks');
  Route::get('/admin/content/tasks/add', [Admin_Content_Task::class, 'add'])->name('admin.content.task.add');
  Route::post('/admin/content/tasks/add', [Admin_Content_Task::class, 'create'])->name('admin.content.task.create');
  Route::get('/admin/content/task/edit/{id}', [Admin_Content_Task::class, 'edit'])->name('admin.content.task.edit');
  Route::get('/admin/content/task/save/{id}', [Admin_Content_Task::class, 'save'])->name('admin.content.task.save');
  Route::post('/admin/content/task/delete', [Admin_Content_Task::class, 'delete'])->name('admin.content.task.delete');   
  Route::get('/admin/content/tasks/download/{id}', [Admin_Content_Task::class, 'download'])->name('admin.content.task.download');   
  // Content -> Task -> Questions
  Route::get('/admin/content/task/{id}/questions', [Admin_Content_Task::class, 'questions'])->name('admin.content.task.questions');
  Route::get('/admin/content/task/{id}/questions/add', [Admin_Content_Task::class, 'question_add'])->name('admin.content.task.question.add');
  Route::post('/admin/content/task/{id}/questions/add', [Admin_Content_Task::class, 'question_create'])->name('admin.content.task.question.create');
  Route::post('/admin/content/task/{id}/questions/reorder', [Admin_Content_Task::class, 'questions_reorder'])->name('admin.content.task.questions.reorder');
  // Content -> Tasks -> Questions -> Question
  Route::post('/admin/content/task/{id}/question/{questionId}/delete', [Admin_Content_Task::class, 'question_delete'])->name('admin.content.task.question.delete');
  Route::get('/admin/content/task/{id}/question/{questionId}/edit', [Admin_Content_Task::class, 'question_edit'])->name('admin.content.task.question.edit');
  Route::post('/admin/content/task/{id}/question/{questionId}/save', [Admin_Content_Task::class, 'question_save'])->name('admin.content.task.question.save');
  // Content -> Tasks -> Questions -> Question -> Inputs
  Route::get('/admin/content/task/{id}/question/{questionId}/inputs', [Admin_Content_Task::class, 'question_inputs'])->name('admin.content.task.question.inputs');
  // Route::post('/admin/content/pillars/{id}/question/{questionId}/inputs/reorder', [Admin_Content_Pillar::class, 'pillar_question_inputs_reorder'])->name('admin.content.pillar.question.inputs.reorder');
  // Route::get('/admin/content/pillars/{id}/question/{questionId}/input/add', [Admin_Content_Pillar::class, 'pillar_question_input_add'])->name('admin.content.pillar.question.input.add');
  // Route::post('/admin/content/pillars/{id}/question/{questionId}/input/create', [Admin_Content_Pillar::class, 'pillar_question_input_create'])->name('admin.content.pillar.question.input.create');
  // Route::get('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/edit', [Admin_Content_Pillar::class, 'pillar_question_input_edit'])->name('admin.content.pillar.question.input.edit');
  // Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/save', [Admin_Content_Pillar::class, 'pillar_question_input_save'])->name('admin.content.pillar.question.input.save');
  // Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/delete', [Admin_Content_Pillar::class, 'pillar_question_input_delete'])->name('admin.content.pillar.question.input.delete');
  // Content -> Tasks -> Questions -> Question -> Actions
  Route::get('/admin/content/task/{id}/question/{questionId}/actions', [Admin_Content_Task::class, 'question_actions'])->name('admin.content.task.question.actions');

  /**
   * Submissions
   *  -> Overview
   *  -> Lifecycle
   *  -> Tasks
   */
  // Submissions -> Overview
  Route::get('/admin/records/submissions', [Admin_Records_Submissions::class, 'index'])->name('admin.records.submissions');
  Route::get('/admin/records/submission/{id}', [Admin_Records_Submissions::class, 'view'])->name('admin.records.submission.view');
  Route::get('/admin/records/submissions/download', [Admin_Records_Submissions::class, 'index'])->name('admin.records.submission.download');
  

















  Route::get('/admin/pillars/tasks', [Admin_Content_Task::class, 'index'])->name('admin.content.pillar.tasks');

  // Content -> Security Controls
  Route::get('/admin/content/securitycontrols', [AdminController::class, 'home'])->name('admin.content.securitycontrols');

  /**
   * Submissions
   */
  Route::get('/admin/submissions', [AdminController::class, 'home'])->name('admin.submissions');
  Route::get('/admin/submissions/overview', [AdminController::class, 'home'])->name('admin.submissions.overview');
  Route::get('/admin/submissions/lifecycle', [AdminController::class, 'home'])->name('admin.submissions.lifecycle');
  Route::get('/admin/submissions/tasks', [AdminController::class, 'home'])->name('admin.submissions.tasks');

  Route::get('/admin/services', [AdminController::class, 'home'])->name('admin.services');
  Route::get('/admin/services/accreditations', [AdminController::class, 'home'])->name('admin.services.accreditations');

  /**
   * Configuration
   */
  // Configuration -> Settings
  Route::get('/admin/configuration/settings', [Admin_Configuration_SiteSettings::class, 'index'])->name('admin.configuration.settings');
  Route::post('/admin/configuration/settings', [Admin_Configuration_SiteSettings::class, 'save'])->name('admin.configuration.settings.save');
  Route::get('/admin/configuration/settings/theme', [Admin_Configuration_SiteSettings::class, 'theme'])->name('admin.configuration.settings.theme');
  Route::post('/admin/configuration/settings/theme', [Admin_Configuration_SiteSettings::class, 'theme_save'])->name('admin.configuration.settings.theme.save');
  Route::get('/admin/configuration/settings/images', [Admin_Configuration_SiteSettings::class, 'theme'])->name('admin.configuration.settings.images');
  Route::get('/admin/configuration/settings/alert', [Admin_Configuration_SiteSettings::class, 'theme'])->name('admin.configuration.settings.alert');

  // Configuration -> Email
  Route::get('/admin/configuration/email', [AdminController::class, 'home'])->name('admin.configuration.email');
  // Configuration -> Risks
  Route::get('/admin/configuration/risks', [Admin_Configuration_Risks::class, 'index'])->name('admin.configuration.risks');
  Route::get('/admin/configuration/risks/add', [Admin_Configuration_Risks::class, 'add'])->name('admin.configuration.risk.add');
  Route::post('/admin/configuration/risks/add', [Admin_Configuration_Risks::class, 'create'])->name('admin.configuration.risk.create');
  Route::get('/admin/configuration/risks/edit/{id}', [Admin_Configuration_Risks::class, 'edit'])->name('admin.configuration.risk.edit');
  Route::post('/admin/configuration/risks/save/{id}', [Admin_Configuration_Risks::class, 'save'])->name('admin.configuration.risk.save');
  Route::post('/admin/configuration/risks/delete', [Admin_Configuration_Risks::class, 'delete'])->name('admin.configuration.risk.delete');   
  // Configuration -> Single Sign-On  
  Route::get('/admin/configuration/sso', [AdminController::class, 'home'])->name('admin.configuration.sso');
});








require __DIR__.'/auth.php';

