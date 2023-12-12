<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\AuditLog;
use App\Models\Configuration;
use App\Models\Pillar;
use App\Http\Requests\AdminContentDashboardUpdateRequest;

class DashboardController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $dashboard = json_decode(Configuration::GetDashboardConfig()->value);
    return Inertia::render('Admin/Content/Dashboard', [
      'siteConfig' => $config,
      'dashboard' => $dashboard
    ]); 
  }

  /**
   * Update our Dashboard Configuration
   */
  public function update(AdminContentDashboardUpdateRequest $request) : RedirectResponse {    
    $siteConfig = Configuration::UpdateDashboardConfig($request->validated());     
    AuditLog::Log("Content.Dashboard Update", $request);
    
    return Redirect::route('admin.content.dashboard');
  }

    /**
   * Handle the default GET of / for this controller
   */
  public function pillars(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $pillars = Pillar::where('id', '>', 0)->orderBy('sort_order')->get();
    return Inertia::render('Admin/Content/Dashboard/Pillars', [
      'siteConfig' => $config,
      'pillars' => $pillars
    ]); 
  }

  /**
   * Handle reordering update for our Pillars
   */
  public function updatePillarOrder(Request $request) {    
    $config = Configuration::GetSiteConfig()->value;
    $pillars = Pillar::where('id', '>', 0)->orderBy('sort_order')->get();

    $newOrder = $request->input("newOrder");
    for ($index = 0; $index < sizeof($newOrder); $index++) {  
      $pillarId = $newOrder[$index];  
      foreach($pillars as $pillar) {
        if ($pillar->id == $pillarId) {
          $pillar->sort_order = $index;
          $pillar->save();
        }
      }
    }

    return Redirect::route('admin.content.dashboard.pillars');
  }


};