<?php
namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Configuration;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
  /**
   * GET /home/auditlog
   * 
   * Display the list of audit log entries in reverse chronological order
   */
  public function index(Request $request) {
    $auditLog = AuditLog::orderByDesc('created_at')->paginate(20);
    
    return Inertia::render('Admin/Home/AuditLog', [
      'siteConfig' => Configuration::site_config(),
      'auditLog' => $auditLog
    ]); 
  }

  /**
   * GET /home/auditlog/{auditId}
   * 
   * View an individual audit log record
   */
  public function view(Request $request, $auditId) {
    $entry = AuditLog::findOrFail($auditId);
    
    return Inertia::render('Admin/Home/AuditLog.View', [
      'siteConfig' => Configuration::site_config(),
      'entry' => $entry
    ]); 
  }

};