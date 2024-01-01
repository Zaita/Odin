<?php

use App\Http\Controllers\Admin\Home\AuditLogController as Admin_Home_AuditLog;
use App\Http\Controllers\Admin\ContentDashboardController;
use App\Http\Controllers\Admin\Content\DashboardController as Admin_Content_Dashboard;
use App\Http\Controllers\Admin\Content\PillarController as Admin_Content_Pillar;
use App\Http\Controllers\Admin\Content\TaskController as Admin_Content_Task;
use App\Http\Controllers\Admin\Security\GroupController as Admin_Security_Group;
use App\Http\Controllers\Admin\Security\UserController as Admin_Security_User;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AdminSiteConfigurationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\PillarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
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
  Route::get('/help', [HelpController::class, 'index'])->name('help');
  Route::get('/start/{pillarId}', [PillarController::class, 'start'])->name('pillar.start');
  Route::post('/start/{pillarId}', [SubmissionController::class, 'start'])->name('submission.start');
  Route::get('/view/{pillarId}', [SubmissionController::class, 'view'])->name('submission.view');
  Route::get('/inprogress/{uuid}', [SubmissionController::class, 'inProgress'])->name('submission.inprogress');
  Route::post('/inprogress/{uuid}', [SubmissionController::class, 'update'])->name('submission.update');
  Route::get('/review/{uuid}', [SubmissionController::class, 'review'])->name('submission.review');
  Route::get('/submit/{uuid}', [SubmissionController::class, 'submit'])->name('submission.submit');
  Route::get('/submitted/{uuid}', [SubmissionController::class, 'submitted'])->name('submission.submitted');
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
  // General Pillars
  Route::get('/admin/content/pillars', [Admin_Content_Pillar::class, 'index'])->name('admin.content.pillars');
  Route::get('/admin/content/pillars/add', [Admin_Content_Pillar::class, 'add'])->name('admin.content.pillars.add');
  Route::post('/admin/content/pillars/add', [Admin_Content_Pillar::class, 'create'])->name('admin.content.pillar.create');
  Route::get('/admin/content/pillars/edit/{id}', [Admin_Content_Pillar::class, 'edit'])->name('admin.content.pillar.edit');
  Route::post('/admin/content/pillars/save', [Admin_Content_Pillar::class, 'save'])->name('admin.content.pillar.save');   
  Route::post('/admin/content/pillars/delete', [Admin_Content_Pillar::class, 'delete'])->name('admin.content.pillars.delete');   
  Route::get('/admin/content/pillar/download/{id}', [Admin_Content_Pillar::class, 'download'])->name('admin.content.pillar.download');   

  // Editing a specific Pillar now
  Route::get('/admin/content/pillars/{id}/questions', [Admin_Content_Pillar::class, 'pillar_questions_index'])->name('admin.content.pillar.questions');
  Route::get('/admin/content/pillars/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_add'])->name('admin.content.pillar.question.add');
  Route::post('/admin/content/pillars/{id}/questions/add', [Admin_Content_Pillar::class, 'pillar_questions_create'])->name('admin.content.pillar.question.create');
  Route::post('/admin/content/pillars/{id}/questions/reorder', [Admin_Content_Pillar::class, 'pillar_questions_reorder'])->name('admin.content.pillar.questions.reorder');
  // Editing a specific question now
  Route::post('/admin/content/pillars/{id}/questions/{questionId}/delete', [Admin_Content_Pillar::class, 'pillar_question_delete'])->name('admin.content.pillar.question.delete');
  Route::get('/admin/content/pillars/{id}/questions/{questionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_edit'])->name('admin.content.pillar.question.edit');
  Route::post('/admin/content/pillars/{id}/questions/{questionId}/save', [Admin_Content_Pillar::class, 'pillar_question_save'])->name('admin.content.pillar.question.save');
  // Question -> Inputs
  Route::get('/admin/content/pillars/{id}/question/{questionId}/inputs', [Admin_Content_Pillar::class, 'pillar_question_inputs'])->name('admin.content.pillar.question.inputs');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/inputs/reorder', [Admin_Content_Pillar::class, 'pillar_question_inputs_reorder'])->name('admin.content.pillar.question.inputs.reorder');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/input/add', [Admin_Content_Pillar::class, 'pillar_question_input_add'])->name('admin.content.pillar.question.input.add');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/create', [Admin_Content_Pillar::class, 'pillar_question_input_create'])->name('admin.content.pillar.question.input.create');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/edit', [Admin_Content_Pillar::class, 'pillar_question_input_edit'])->name('admin.content.pillar.question.input.edit');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/save', [Admin_Content_Pillar::class, 'pillar_question_input_save'])->name('admin.content.pillar.question.input.save');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/input/{inputId}/delete', [Admin_Content_Pillar::class, 'pillar_question_input_delete'])->name('admin.content.pillar.question.input.delete');
  // Question -> Actions
  Route::get('/admin/content/pillars/{id}/question/{questionId}/actions', [Admin_Content_Pillar::class, 'pillar_question_actions'])->name('admin.content.pillar.question.actions');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/actions/reorder', [Admin_Content_Pillar::class, 'pillar_question_actions_reorder'])->name('admin.content.pillar.question.actions.reorder');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/action/add', [Admin_Content_Pillar::class, 'pillar_question_action_add'])->name('admin.content.pillar.question.action.add');
  Route::post('/admin/content/pillars/{id}/question/{questionId}/action/create', [Admin_Content_Pillar::class, 'pillar_question_action_create'])->name('admin.content.pillar.question.action.create');
  Route::get('/admin/content/pillars/{id}/question/{questionId}/action/{actionId}/edit', [Admin_Content_Pillar::class, 'pillar_question_action_edit'])->name('admin.content.pillar.question.action.edit');

  // Content -> Tasks
  Route::get('/admin/content/tasks', [Admin_Content_Task::class, 'index'])->name('admin.content.tasks');
  Route::get('/admin/content/tasks/add', [Admin_Content_Task::class, 'add'])->name('admin.content.task.add');
  Route::post('/admin/content/tasks/add', [Admin_Content_Task::class, 'create'])->name('admin.content.task.create');
  // Route::get('/admin/content/tasks/edit/{id}', [Admin_Content_Task::class, 'edit'])->name('admin.content.pillar.edit');
  // Route::post('/admin/content/tasks/save', [Admin_Content_Task::class, 'save'])->name('admin.content.pillar.save');   
  // Route::post('/admin/content/tasks/delete', [Admin_Content_Task::class, 'delete'])->name('admin.content.pillars.delete');   
  Route::get('/admin/content/tasks/download/{id}', [Admin_Content_Task::class, 'download'])->name('admin.content.task.download');   

















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
  Route::get('/admin/configuration', [AdminController::class, 'home'])->name('admin.configuration');
  Route::get('/admin/configuration/site', [AdminSiteConfigurationController::class, 'index'])->name('admin.configuration.siteconfig');
  Route::patch('/admin/configuration/site', [AdminSiteConfigurationController::class, 'update'])->name('admin.configuration.siteconfig.update');
  Route::get('/admin/configuration/email', [AdminController::class, 'home'])->name('admin.configuration.email');
  Route::get('/admin/configuration/risks', [AdminController::class, 'home'])->name('admin.configuration.risks');
  Route::get('/admin/configuration/sso', [AdminController::class, 'home'])->name('admin.configuration.sso');
});








require __DIR__.'/auth.php';

