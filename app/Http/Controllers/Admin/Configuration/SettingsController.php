<?php
namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Http\Requests\SiteSettingsRequest;
use App\Http\Requests\ThemeRequest;

class SettingsController extends Controller
{
  /**
   * GET /Admin/Configuration/Settings
   */
  public function index(Request $request) {
    return Inertia::render('Admin/Configuration/Settings', [
      'siteConfig' => Configuration::site_config(),
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

  /**
   * GET /Admin/Configuration/Settings/Theme
   */
  public function theme(Request $request) {
    return Inertia::render('Admin/Configuration/Settings.Theme', [
      'siteConfig' => Configuration::site_config(),
    ]); 
  }
  
  /**
   * POST /Admin/Configuration/Settings/Theme
   */
  public function theme_save(ThemeRequest $request): RedirectResponse
  {
      Configuration::UpdateTheme($request->validated());
      return Redirect::route('admin.configuration.settings.theme');
  }
};