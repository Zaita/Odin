<?php
namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\AuditLog;

use App\Http\Requests\RiskRequest;

class EmailController extends Controller
{
  /**
   * GET /admin/configuration/email
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    
    return Inertia::render('Admin/Configuration/Email', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * GET /admin/configuration/email/start
   * Load the add screen
   */
  public function start(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Configuration/Email/Start', [
      'siteConfig' => $config,
    ]); 
  }
};