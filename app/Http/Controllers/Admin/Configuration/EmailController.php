<?php
namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailMainRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Email;

class EmailController extends Controller
{
  /**
   * GET /admin/configuration/email
   */
  public function index(Request $request) {   
    return Inertia::render('Admin/Configuration/Email', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * POST /admin/configuration/email
   * Handle updating the 'main' email configuration options
   */
  public function save(EmailMainRequest $request) {
    $email = new Email();
    if (!$email->updateMainSettings($request)) {
      return back()->withInput()->with('error', $email->error);
    }

    return redirect()->route('admin.configuration.email');
  }

  /**
   * GET /admin/configuration/email/start
   * Load the add screen
   */
  public function start(Request $request) {
    return Inertia::render('Admin/Configuration/Email.Start', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }
};