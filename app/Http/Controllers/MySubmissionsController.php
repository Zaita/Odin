<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\Submission;

class MySubmissionsController extends Controller
{
  public function index(Request $request) {
    Log::Info("Loading My Submissions (/submissions)");
    $pillars = Pillar::all();
    $config = json_decode(Configuration::GetSiteConfig()->value);

    $user = $request->user();
    $latestSubmissions = Submission::where("submitter_email", $user->email)->latest()->limit(10)->get();

    // return response()->json($latestSubmissions);

    return Inertia::render('Submissions', [
      'siteConfig' => $config,
      'latestSubmissions' => $latestSubmissions
    ]);

  }
}
