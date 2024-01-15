<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\Pillar;
use App\Models\Submission;

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
    $latestSubmissions = Submission::where("submitter_email", $user->email)->latest()->limit(5)->get();
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
      ->whereNotIn('status', ['not_approved', 'approved', 'expired'])
      ->orderByDesc('id', 'created_at', 'updated_at', 'product_name')->paginate(20);

    return Inertia::Render('Submissions', [
      'siteConfig' => $config,
      'submissions' => $submissions
    ]);
  }

  public function approvals(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Approvals', [
      'siteConfig' => $config,
    ]);    
  }

  public function help(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Help', [
      'siteConfig' => $config,
    ]);    
  }
}
