<?php
namespace App\Http\Controllers\Admin\Records;

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
use App\Models\Submission;

class SubmissionsController extends Controller
{
  /**
   * GET /admin/records/submissions
   * 
   * Display a list of all non-expired submissions
   */
  public function index(Request $request) {
    AuditLog::Log("Submissions.List", $request);
    $submissions = Submission::orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]); 
  }

  /**
   * GET /admin/records/submissions/inprogress
   * 
   * Display the list of submissions that are currently in progress
   */
  public function in_progress(Request $request) {
    AuditLog::Log("Submissions.List.InProgress", $request);
    $submissions = Submission::where('status', '=', 'in_progress')->orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]); 
  }

   /**
   * GET /admin/records/submissions/waitingforapproval
   * 
   * Display the list of submissions that are waiting for an approval or endorsement
   */
  public function waiting_for_approval(Request $request) {
    AuditLog::Log("Submissions.List.WaitingForApproval", $request);
    $submissions = Submission::where('status', '=', 'waiting_for_approval')->orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]); 
  }

  /**
   * GET /admin/records/submissions/approved
   * 
   * Display the list of submissions that have been approved
   */
  public function approved(Request $request) {
    AuditLog::Log("Submissions.List.WaitingForApproval", $request);
    $submissions = Submission::where('status', '=', 'approved')->orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]);     
  }

  /**
   * GET /admin/records/submissions/denied
   * 
   * Display the list of submissions that have been denied
   */
  public function denied(Request $request) {
    AuditLog::Log("Submissions.List.Denied", $request);
    $submissions = Submission::where('status', '=', 'denied')->orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]); 
  }

  /**
   * GET /admin/records/submissions/expired
   * 
   * Display the list of submissions that have expired
   */
  public function expired(Request $request) {
    AuditLog::Log("Submissions.List.Expired", $request);
    $submissions = Submission::where('status', '=', 'expired')->orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => Configuration::site_config(),
      'submissions' => $submissions
    ]); 
  }

  /**
   * GET /admin/records/submission/{id}
   * 
   * Get the main information for the submission
   */
  public function view(Request $request, $submissionId) {
    AuditLog::Log("Submission($submissionId).View", $request);
    $submission = Submission::findOrFail($submissionId);
    
    return Inertia::render('Admin/Records/Submissions/Submission.View', [
      'siteConfig' => Configuration::site_config(),
      'submission' => $submission
    ]); 
  }
  
};