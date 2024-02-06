<?php
namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Group;
use App\Http\Requests\AdminSecurityGroupRequest;

class GroupController extends Controller
{
  // Groups required to be present for approval flows
  protected $protectedGroups = array("Administrator", "Security Architect", "Chief Information Security Officer");

  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $groups = Group::get();
    // Grab any errors from the a failed create/save/delete
    $errors = $request->session()->get('errors') ?? null;
    $request->session()->forget('errors');
    
    return Inertia::render('Admin/Security/Groups', [
      'siteConfig' => Configuration::site_config(),
      'groups' => $groups,
      'errors' => $errors
    ]); 
  }

  /**
   * Delete a group. We have some protected groups that we don't
   * allow to be removed as they are necessary for the approval
   * flows 
   */
  public function delete(Request $request)  {  
    AuditLog::Log("Security.Group.Delete", $request);
    $id = $request->input('id', -1);
    $group = Group::findOrFail($id); 
    
    if (in_array($group->name, $this->protectedGroups)) {
      $request->session()->flash('errors', "Cannot delete a protected group");
    
    } else {
      $deleted = Group::where('id', $id)->delete();   
    }

    return Redirect::route('admin.security.groups');
  }

  /**
   * Show the add page
   */
  public function add(Request $request) {
    return Inertia::render('Admin/Security/Group.Add', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * Create a new Security Group
   */
  public function create(AdminSecurityGroupRequest $request) : RedirectResponse {
    AuditLog::Log("Security.Group.Add", $request);  
    Group::create($request->validated());     
    return Redirect::route('admin.security.groups');
  }

  /**
   * Load the edit screen for an existing group
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Security.Group.Edit", $request);
    $group = Group::findOrFail($id); 
    
    if (in_array($group->name, $this->protectedGroups)) {
      Log::Info("Protected");
      $request->session()->flash('errors', "Cannot edit a protected group");
      return Redirect::route('admin.security.groups');
    } 

    return Inertia::render('Admin/Security/Group.Edit', [
      'siteConfig' => Configuration::site_config(),
      'group' => $group
    ]); 
  }

  /**
   * Save changes to our existing group
   */
  public function save(AdminSecurityGroupRequest $request) : RedirectResponse {
    AuditLog::Log("Security.Group.Save", $request);
    $id = $request->input('id', -1);
    $group = Group::findOrFail($id);     
    $group->update($request->validated());
    return Redirect::route('admin.security.groups');
  }

};