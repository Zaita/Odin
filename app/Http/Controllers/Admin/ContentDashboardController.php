<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Configuration;
use App\Models\Pillar;

class ContentDashboardController extends Controller
{
  public function tasks(Request $request) {
    $config = Configuration::GetSiteConfig()->value;
    $pillars = Pillar::where('id', '>', 0)->orderBy('sort_order')->get();
    return Inertia::render('Admin/Content/Dashboard/Tasks', [
      'siteConfig' => $config,
      'pillars' => $pillars
    ]); 
  }
}
