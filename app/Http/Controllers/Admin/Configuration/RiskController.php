<?php
namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Risk;

use App\Http\Requests\RiskRequest;

class RiskController extends Controller
{
  /**
   * GET /admin/configuration/risks
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $risks = Risk::orderBy('created_at')->get();
    
    return Inertia::render('Admin/Configuration/Risks', [
      'siteConfig' => $config,
      'risks' => $risks
    ]); 
  }

  /**
   * GET /admin/configuration/risk/add
   * Load the add screen
   */
  public function add(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Configuration/Risks.Add', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * POST /admin/configuration/risk/create
   * 
   * Create a new risk object in the database
   */
  public function create(RiskRequest $request) : RedirectResponse {
    AuditLog::Log("Configuration.Risks.Add", $request);    
    $r = Risk::firstOrCreate($request->safe()->only(['name', 'description']));    
    // return Redirect::route('admin.configuration.risk.edit', $r->id);
    return Redirect::route('admin.configuration.risks');
  }

  /**
   * Delete a group. We have some protected groups that we don't
   * allow to be removed as they are necessary for the approval
   * flows 
   */
  public function delete(Request $request)  {  
    AuditLog::Log("Configuration.Risks.Delete", $request);
    $id = $request->input('id', -1);
    $risk = Risk::findOrFail($id);
    $risk->delete();
    return Redirect::route('admin.configuration.risks');
  }

  /**
   * **************************************************************************
   * Below we handle specific routes for working with a singular pillar
   * e.g. adding/modifying questions
   * **************************************************************************
   */

  /**
   * Load the edit screen for an existing pillar
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Configuration.Risk.Edit", $request);
    $risk = Risk::findOrFail($id);
        
    return Inertia::render('Admin/Configuration/Risks.Edit', [
      'siteConfig' => json_decode(Configuration::GetSiteConfig()->value),
      'risk' => $risk
    ]); 
  }

  /**
   * Save changes to our existing group
   */
  public function save(RiskRequest $request, $id) : RedirectResponse {
    AuditLog::Log("Configuration.Risk.Save", $request);
    $risk = Risk::findOrFail($id);
    $risk->update($request->validated());
    
    if (!$risk->save()) {
      return back()->withInput()->withErrors($risk->errors);
    }
    return Redirect::route('admin.configuration.risk.edit', $id);
  }
};