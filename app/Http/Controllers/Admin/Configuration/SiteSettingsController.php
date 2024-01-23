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
use App\Http\Requests\SiteSettingsRequest;

class SiteSettingsController extends Controller
{
  /**
   * GET /Admin/Configuration/Settings
   */
  public function index(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Configuration/Settings', [
      'siteConfig' => $config,
    ]); 
  }

  /**
   * POST /Admin/Configuration/Settings
   */
  public function save(SiteSettingsRequest $request): RedirectResponse
  {
      Configuration::UpdateSiteConfig($request->validated());
      return Redirect::route('admin.configuration.settings');
  }

  public function theme(Request $request) {
    $config = json_decode(Configuration::GetSiteConfig()->value);
    return Inertia::render('Admin/Configuration/Settings/Theme', [
      'siteConfig' => $config,
    ]); 
  }

  
  public function theme_save(SiteSettingsRequest $request): RedirectResponse
  {
      Configuration::UpdateSiteConfig($request->validated());
      return Redirect::route('admin.configuration.settings.theme');
  }
};