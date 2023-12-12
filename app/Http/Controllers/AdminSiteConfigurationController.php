<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Configuration;
use App\Http\Requests\AdminSiteConfigUpdateRequest;


class AdminSiteConfigurationController extends Controller
{
  public function index(Request $request) {
    $config = Configuration::GetSiteConfig()->value;
    return Inertia::render('Admin/Configuration/SiteConfiguration', [
      'siteConfig' => $config
    ]);    
  }  

  public function update(AdminSiteConfigUpdateRequest $request) : RedirectResponse {    
    $siteConfig = Configuration::UpdateSiteConfig($request->validated());   
    return Redirect::route('admin.configuration.siteconfig');
  }
}
