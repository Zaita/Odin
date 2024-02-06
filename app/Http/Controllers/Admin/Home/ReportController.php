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
use App\Models\Report;

class ReportController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $reports = Report::all();
    
    return Inertia::render('Admin/Home/Reports', [
      'siteConfig' => Configuration::site_config(),
      'reports' => $reports
    ]); 
  }

  public function execute(Request $request, $id) {
    $report = Report::findOrFail($id);
    $report->execute();

    return Inertia::render('Admin/Home/Reports.View', [
      'siteConfig' => Configuration::site_config(),
      'title' => $report->name,
      'header' => $report->header,
      'rows' => $report->rows,
    ]); 
  }
};