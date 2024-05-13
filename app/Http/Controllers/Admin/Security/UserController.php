<?php
namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\UniqueConstraintViolationException;

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupUser;
use App\Http\Requests\AdminSecurityUserRequest;
use App\Http\Requests\AdminSecurityUserEditRequest;

class UserController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    AuditLog::Log("Security.Users.User", $request);
    return Inertia::render('Admin/Security/Users', [
      'siteConfig' => Configuration::site_config(),
      'users' => User::orderBy('created_at')->paginate(20),
    ]); 
  }

  /**
   * Delete a group. We have some protected groups that we don't
   * allow to be removed as they are necessary for the approval
   * flows 
   */
  public function delete(Request $request)  {  
    AuditLog::Log("Security.User.Delete", $request);
    $id = $request->input('id', -1);
    $group = User::findOrFail($id); 
    
    if (false) {
      // Add code to preventing the last admin, or the current user
      $request->session()->flash('errors', "Cannot delete a protected group");
    
    } else {
      $deleted = User::where('id', $id)->delete();   
    }

    return Redirect::route('admin.security.users');
  }

  /**
   * Show the add page
   */
  public function add(Request $request) {
    return Inertia::render('Admin/Security/User.Add', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * Create a new Security Group
   */
  public function create(AdminSecurityUserRequest $request) : RedirectResponse {
    AuditLog::Log("Security.User.Add", $request);   

    try {    
      $user = User::create($request->validated());
    } catch (UniqueConstraintViolationException $e) {
      return back()->withErrors(["save" => "Create Failed: User email has already been registered"]);
    }
    
    return Redirect::route('admin.security.user.edit', $user->id)->with('saveOk', 'New user added successfully');
  }

  /**
   * Load the edit screen for an existing group
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Security.User.Edit", $request);
    $user = User::findOrFail($id); 
    return Inertia::render('Admin/Security/User.Edit', [
      'siteConfig' => Configuration::site_config(),
      'user' => $user
    ]); 
  }

  /**
   * Save changes to our existing group
   */
  public function save(AdminSecurityUserEditRequest $request, $id) : RedirectResponse {
    AuditLog::Log("Security.User.Save", $request);

    try {    
      $user = User::findOrFail($id); 
      $user->update($request->validated());
      $user->save();
    } catch (UniqueConstraintViolationException $e) {
      return back()->withErrors(["save" => "Save Failed: User email has already been registered"]);
    }
    
    return Redirect::route('admin.security.user.edit', $id);
  }

  /**
   * Load the edit screen for an existing group
   */
  public function groups(Request $request, $id) {
    AuditLog::Log("Security.User.Groups", $request);
    $user = User::findOrFail($id); 

    $groupOptions = array();
    foreach(Group::all() as $group ) {
      array_push($groupOptions, $group->name);
    }

    return Inertia::render('Admin/Security/User.Groups', [
      'siteConfig' => Configuration::site_config(),
      'user' => $user,
      'groupOptions' => $groupOptions
    ]); 
  }

  /**
   * Add the user to a security group
   */
  public function linkToGroup(Request $request, $userId) {
    AuditLog::Log("Security.User.Link", $request);
    $user = User::findOrFail($userId); 
    $group = Group::where("name", $request->input('group', ''))->firstOrFail();

    GroupUser::createOrFirst([
      'user_id' => $user->id,
      'group_id' => $group->id
    ]);

    return Redirect::route('admin.security.user.groups', $userId);
  }

  /**
   * Delete the user from a security group
   */
  public function unlinkFromGroup(Request $request, $userId) {
    AuditLog::Log("Security.User.Unlink", $request);
    $user = User::findOrFail($userId); 
    $group = Group::where("id", $request->input('id', ''))->firstOrFail();

    GroupUser::where([
      'user_id' => $user->id,
      'group_id' => $group->id
    ])->delete();

    return Redirect::route('admin.security.user.groups', $userId);
  }

};