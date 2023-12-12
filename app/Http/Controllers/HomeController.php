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
}
