<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\Pillar;
use App\Models\Questionnaire;

class PillarController extends Controller
{
    public function start(Request $request, $pillarId) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      $pillar = Pillar::where('id', $pillarId)->first();
      if ($pillar == null) {
        Log::Emergency("Pillar is null");
      }

      return Inertia::render('Start', [
        'siteConfig' => $config,
        'pillar' => $pillar,
      ]);   
    }
}
