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
    AuditLog::Log("Security.User.User", $request);
    $users = User::orderBy('created_at')->paginate(20);
    // Grab any errors from the a failed create/save/delete
    $errors = $request->session()->get('errors') ?? null;
    $request->session()->forget('errors');
    
    return Inertia::render('Admin/Security/User', [
      'siteConfig' => Configuration::site_config(),
      'users' => $users,
      'errors' => $errors
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
    return Inertia::render('Admin/Security/User/Add', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }

  /**
   * Create a new Security Group
   */
  public function create(AdminSecurityUserRequest $request) : RedirectResponse {
    AuditLog::Log("Security.User.Add", $request);    
    $user = User::firstOrNew($request->safe()->only(['email']));
    if ($user->exists) {
        $request->session()->flash('errors', "User with email {$user->email} already exists");
    } else {
      User::create($request->validated());
    }    
    
    return Redirect::route('admin.security.users');
  }

  /**
   * Load the edit screen for an existing group
   */
  public function edit(Request $request, $id) {
    AuditLog::Log("Security.User.Edit", $request);
    $user = User::findOrFail($id); 
    return Inertia::render('Admin/Security/User/Edit', [
      'siteConfig' => Configuration::site_config(),
      'user' => $user
    ]); 
  }

  /**
   * Save changes to our existing group
   */
  public function save(AdminSecurityUserEditRequest $request) : RedirectResponse {
    AuditLog::Log("Security.User.Save", $request);
    $id = $request->input('id', -1);
    $user = User::findOrFail($id); 
    $user->update($request->validated());
    return Redirect::route('admin.security.users');
  }

  /**
   * Add the user to a security group
   */
  public function addToGroup(Request $request) {
    AuditLog::Log("Security.User.AddToGroup", $request);
    $id = $request->input('user_id', -1);
    $user = User::findOrFail($id); 
    $group = Group::where("name", $request->input('name', ''))->firstOrFail();

    GroupUser::create([
      'user_id' => $user->id,
      'group_id' => $group->id
    ]);

    return Redirect::route('admin.security.users.edit', $id);
  }

};