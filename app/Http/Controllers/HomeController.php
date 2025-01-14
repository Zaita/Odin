<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\HelpItem;
use App\Models\Pillar;
use App\Models\Submission;
use App\Models\SecurityCatalogue;
use App\Models\SecurityControl;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
  public function error(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Error', [
      'siteConfig' => $config,
    ]); 
  }

  public function index(Request $request) {
    Log::Info("Loading Home");
    $pillars = Pillar::all();
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $dashboardConfig = json_decode(Configuration::GetDashboardConfig()->value);

    $user = $request->user();
    $latestSubmissions = Submission::where("submitter_email", $user->email)
      ->whereNotIn('status', ['expired'])
      ->latest()->limit(5)->get();
    return Inertia::render('Home', [
      'siteConfig' => $config,
      'dashboard' => $dashboardConfig,
      'pillars' => $pillars,
      'latestSubmissions' => $latestSubmissions
    ]);    
  }

  /**
   * GET /submissions
   * Load the user submissions
   */
  public function submissions(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $user = $request->user();
    $submissions = Submission::where("submitter_email", $user->email)
      // ->whereNotIn('status', ['not_approved', 'approved', 'expired'])
      ->whereNotIn('status', ['expired'])
      ->orderByDesc('id')->paginate(20);

    return Inertia::Render('Submissions', [
      'siteConfig' => $config,
      'submissions' => $submissions
    ]);
  }

  /**
   * GET /approvals
   * 
   * Load a list of submissions that can be approved by the current user
   */
  public function approvals(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $user = $request->user();
    $approvableSubmissions = array();
    $submissions = Submission::where(['status' => 'waiting_for_approval'])->orderBy('id')->get();

    foreach($submissions as $submission) {
      if ($submission->canAssignUser($user) || $submission->canApproveWithType($user, null)) {
        array_push($approvableSubmissions, $submission);
      }
    }
    
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 20;
    $paginator = new LengthAwarePaginator($approvableSubmissions, count($approvableSubmissions), $perPage, $currentPage);
    $paginator->withPath('/approvals');

    return Inertia::render('Approvals', [
      'siteConfig' => $config,
      'submissions' => $paginator
    ]);    
  }

  /**
   * GET /help
   * 
   * Display help information
   */
  public function help(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $helpItems = HelpItem::get();
    return Inertia::render('Help', [
      'siteConfig' => $config,
      'items' => $helpItems,
    ]);    
  }

  public function securityControls(Request $request) {
    $id = 1;
    return Inertia::render('SecurityControls', [
      'siteConfig' => Configuration::site_config(),
      'catalogue' => SecurityCatalogue::findOrFail($id),
      'controls' => SecurityControl::where('security_catalogue_id', '=', $id)->orderBy('name', 'asc')->get()
    ]);   
  }
  public function securityControl_view(Request $request, $id) {    
    $control = SecurityControl::findOrFail($id);
    return Inertia::render('SecurityControl.View', [
      'siteConfig' => Configuration::site_config(),
      'catalogue' => SecurityCatalogue::findOrFail($control->security_catalogue_id),
      'control' => $control
    ]);   
  }

}
