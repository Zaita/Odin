<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\Configuration;
use App\Models\Pillar;

class AdminController extends Controller
{
    public function index(Request $request) {
      $config = json_decode(Configuration::GetSiteConfig()->value);

      return Inertia::render('Admin/Home', [
        'siteConfig' => $config
      ]);
    }

    public function reports(Request $request) {
      $config = json_decode(Configuration::GetSiteConfig()->value);

      return Inertia::render('Admin/Home/Reports', [
        'siteConfig' => $config
      ]);
    }

    public function security(Request $request) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      return Inertia::render('Admin/Security', [
        'siteConfig' => $config
      ]);
    }

    public function content(Request $request) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      $pillars = Pillar::all();
      return Inertia::render('Admin/Content', [
        'pillars' => $pillars,
        'siteConfig' => $config
      ]);      
    }
}
