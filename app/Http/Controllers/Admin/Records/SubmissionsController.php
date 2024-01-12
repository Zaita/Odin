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
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submissions = Submission::orderByDesc('id')->paginate(20);
    
    return Inertia::render('Admin/Records/Submissions', [
      'siteConfig' => $config,
      'submissions' => $submissions
    ]); 
  }

  public function view(Request $request, $submissionId) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $submission = Submission::findOrFail($submissionId);
    
    return Inertia::render('Admin/Records/Submissions/View', [
      'siteConfig' => $config,
      'submission' => $submission
    ]); 
  }
  
};