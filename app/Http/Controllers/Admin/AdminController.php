<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Configuration;

class AdminController extends Controller
{
    /**
     * GET /admin
     * 
     * Display the admin dashboard
     */
    public function index(Request $request) {
      return Inertia::render('Admin/Home', [
        'siteConfig' => Configuration::site_config(),
      ]);
    }
}
