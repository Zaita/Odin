<?php
namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

use App\Models\Configuration;
use App\Models\HelpItem;
use App\Models\AuditLog;

class HelpController extends Controller
{
  /**
   * GET /admin/content/securitycatalogues
   */
  public function index(Request $request) {
    return Inertia::render('Admin/Configuration/Help', [
      'siteConfig' => Configuration::site_config(),
      'items' => HelpItem::get()
    ]); 
  }

  /**
   * GET /admin/content/securitycatalogue/add
   * Load the add screen
   */
  public function add(Request $request) {
    return Inertia::render('Admin/Content/SecurityCatalogues/Catalogue.Add', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * POST /admin/content/securitycatalogues/add
   * Create/Save a new catalogue
   */
  public function create(AdminSecurityCatalogueRequest $request) : RedirectResponse {
    AuditLog::Log("Content.SecurityCatalogue.Add", $request);    
    try {    
      $sc = SecurityCatalogue::create($request->validated());
    } catch (UniqueConstraintViolationException $e) {
      return back()->withErrors(["save" => "Create Failed: Security catalogue name has already been used"]);
    }

    return Redirect::route('admin.content.securitycatalogue.edit', $sc->id)->with('saveOk', 'New security catalogue added successfully');
  }

  /**
   * POST /admin/content/securitycatalogue/delete
   */
  public function delete(Request $request)  {  
    AuditLog::Log("Content.SecurityCatalogue.Delete", $request);
    $id = $request->input('id', -1);
    $sc = SecurityCatalogue::findOrFail($id);
    $sc->delete();
    return Redirect::route('admin.content.securitycatalogues');
  }

  /**
   * 
   * Download a pillar as a JSON file
   */
  public function download(Request $request, $id)  {  
    AuditLog::Log("Content.SecurityCatalogue($id).Download", $request);
    return response()->streamDownload(
      function () use ($id) { 
        $catalogue = SecurityCatalogue::with([
          'security_controls' => function(Builder $q) {$q->orderBy('name', 'asc');}, 
          'security_controls.risk_weights',
          'security_controls.risk_weights.risk'])->findOrFail($id);
        echo json_encode($catalogue, JSON_PRETTY_PRINT);
      }
    ,'catalogue.txt');
  }
  

  /**
   * **************************************************************************
   * Below we handle specific routes for working with a catalogue
   * e.g. adding/modifying controls
   * **************************************************************************
   */

  /**
   * GET/admin/content/securitycatalogue/{id}/edit
   * Load the edit screen for an existing pillar
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Content.SecurityCatalogue.Edit", $request);
    return Inertia::render('Admin/Content/SecurityCatalogues/Catalogue.Edit', [
      'siteConfig' => Configuration::site_config(),
      'catalogue' => SecurityCatalogue::findOrFail($id),
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * POST /admin/content/securitycatalogue/{id}/save
   * Save changes to our existing group
   */
  public function save(AdminSecurityCatalogueRequest $request, $id) : RedirectResponse {
    AuditLog::Log("Content.SecurityCatalogue.Save", $request);
    try {    
      $sc = SecurityCatalogue::findOrFail($id);
      $sc->update($request->validated());
      $sc->save();
    } catch (UniqueConstraintViolationException $e) {
      return back()->withErrors(["save" => "Save Failed: Security catalogue name has already been used"]);
    }
    
    return Redirect::route('admin.content.securitycatalogue.edit', $id);
  }

  /**
   * GET /admin/content/securitycatalogue/{id}/controls
   * 
   * Load the control list for the given catalogue
   */
  public function controls(Request $request, $id) {
    return Inertia::render('Admin/Content/SecurityCatalogues/Controls.View', [
      'siteConfig' => Configuration::site_config(),
      'catalogue' => SecurityCatalogue::findOrFail($id),
      'controls' => SecurityControl::where('security_catalogue_id', '=', $id)->orderBy('name', 'asc')->get()
    ]); 
  }

  /**
   * GET /admin/content/securitycatalogue/{id}/control/add
   * 
   * Show the Add control screen for user input
   */
  public function control_add(Request $request, $id) {
    return Inertia::render('Admin/Content/SecurityCatalogues/Control.Add', [
      'siteConfig' => Configuration::site_config(),
      'catalogue' => SecurityCatalogue::findOrFail($id),
      'risks' => Risk::get()
    ]); 
  }

  /**
   * POST /admin/content/securitycatalogue/{id}/control/add
   * 
   * Create the new security control
   */
  public function control_create(SecurityControlRequest $request, $id) : RedirectResponse {
    Log::Info("control_create");
    try {
      $sc = new SecurityControl($request->validated());
      $sc->security_catalogue_id = $id;
      $sc->save(); 
      $sc->updateRisks($request->all());
      Log::Info("Updated Risks");

    } catch (UniqueConstraintViolationException $e) {
      Log::Info("Save Failed");
      return back()->withErrors(["save" => "Save Failed: Security catalogue name has already been used"]);
    }
    
    Log::Info("Routing to Security Control Edit $id =>  $sc->id");
    return Redirect::route('admin.content.securitycontrol.edit', ["id" => $id, "controlId" => $sc->id])->with('saveOk', 'New security control added successfully');
  }

  /**
   * GET/admin/content/securitycatalogue/{id}/control/{controlId}/edit
   * Load the edit screen for an existing pillar
   */
  public function control_edit(Request $request, $id, $controlId) {
    AuditLog::Log("Content.SecurityCatalogue.Control.Edit", $request);

    return Inertia::render('Admin/Content/SecurityCatalogues/Control.Edit', [
      'siteConfig' => Configuration::site_config(),
      'risks' => Risk::all(),
      'catalogue' => SecurityCatalogue::findOrFail($id),
      'control' => SecurityControl::with("risk_weights", "risk_weights.risk")->findOrFail($controlId),
      'saveOk' => $request->session()->get('saveOk'),
    ]); 
  }

  /**
   * POST 
   */
  public function control_save(SecurityControlRequest $request, $id, $controlId) : RedirectResponse {
    AuditLog::Log('Admin.SecurityControl.Save', $request);
    try {
      $sc = SecurityControl::findOrFail($controlId);
      $sc->update($request->validated());
      $sc->updateRisks($request->all());
      $sc->save();
      Log::Info("Updated Risks");

    } catch (UniqueConstraintViolationException $e) {
      Log::Info("Save Failed");
      return back()->withErrors(["save" => "Save Failed: Security control name has already been used"]);
    }
    
    Log::Info("Routing to Security Control Edit $id =>  $sc->id");
    return Redirect::route('admin.content.securitycontrol.edit', ["id" => $id, "controlId" => $controlId])->with('saveOk', 'Security control updated successfully');
  }

  
  /**
   * POST /admin/content/securitycatalogue/delete
   */
  public function control_delete(Request $request, $id, $controlId)  {  
    AuditLog::Log("Content.SecurityCatalogue.Control.Delete", $request);
    $sc = SecurityControl::findOrFail($controlId);
    $sc->delete();
    return Redirect::route('admin.content.securitycatalogue.controls', ["id" => $id])->with('saveOk', 'Control deleted successfully');
  }

};