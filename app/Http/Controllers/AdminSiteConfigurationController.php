<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSiteConfigUpdateRequest;
use App\Models\Configuration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AdminSiteConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $config = Configuration::GetSiteConfig()->value;
        return Inertia::render('Admin/Configuration/SiteConfiguration', [
            'siteConfig' => $config,
        ]);
    }

    public function update(AdminSiteConfigUpdateRequest $request): RedirectResponse
    {
        $siteConfig = Configuration::UpdateSiteConfig($request->validated());
        return Redirect::route('admin.configuration.siteconfig');
    }
}
