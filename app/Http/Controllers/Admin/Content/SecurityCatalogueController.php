<?php
namespace App\Http\Controllers\Admin\Content;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\SecurityCatalogue;
use App\Models\SecurityControl;
use App\Models\Risk;
use App\Http\Requests\AdminSecurityCatalogueRequest;
use App\Http\Requests\SecurityControlRequest;

class SecurityCatalogueController extends Controller
{
  /**
   * GET /admin/content/securitycatalogues
   */
  public function index(Request $request) {
    return Inertia::render('Admin/Content/SecurityCatalogues', [
      'siteConfig' => Configuration::site_config(),
      'catalogues' => SecurityCatalogue::get()
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
    // return response()->streamDownload(
    //   function () use ($pillarId) { 
    //     $pillar = Pillar::with(["questionnaire", 
    //     "questionnaire.questions" => function(Builder $q) {$q->orderBy('sort_order');},
    //     "questionnaire.questions.inputFields",
    //     "questionnaire.questions.actionFields",
    //     ])->findOrFail($pillarId);
    //     echo json_encode($pillar, JSON_PRETTY_PRINT);
    //   }
    // ,'pillar.txt');
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
      'controls' => SecurityControl::where('security_catalogue_id', '=', $id)->get()
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
    $sc = new SecurityControl();
    $sc->update($request->validated());
    $sc->save(); // save before so we can get the id for the risks
    
    $sc->updateRisks($request->all());
    
    return Redirect::route('admin.content.securitycatalogue.controls', $id)->with('saveOk', 'New pillar added successfully');
  }

};