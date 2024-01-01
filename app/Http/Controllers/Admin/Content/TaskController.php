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

use App\Models\Configuration;
use App\Models\AuditLog;
use App\Models\Task;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
  /**
   * Handle the default GET of / for this controller
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    $tasks = Task::orderBy('created_at')->paginate(20);
    
    return Inertia::render('Admin/Content/Tasks', [
      'siteConfig' => $config,
      'tasks' => $tasks
    ]); 
  }

  /**
   * Content -> Tasks -> Add
   * Load the add screen
   */
  public function add(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Content/Tasks/Add', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * Handle POST from Content -> Tasks -> Add
   * Create a new task
   */
  public function create(TaskRequest $request) : RedirectResponse {
    AuditLog::Log("Content.Tasks.Add", $request);  
    
    $obj = Task::firstOrNew(["name" => $request->safe()->only("name")]);
    if (!is_null($obj->id)) {
      return back()->withInput()->withErrors(array("name" => "Name already exists, must be unique across all tasks"));
    }

    $obj->fill($request->validated());
    $obj->save();

    return Redirect::route('admin.content.tasks');
  }
}