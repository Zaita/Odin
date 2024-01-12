<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\ApprovalFlow;
use App\Models\Group;

class ApprovalFlowSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $fileName = "storage/content/approvalFlows/default.json";
    $file = fopen($fileName, "r") or die("Unable to open file!");
    $stringValue = fread($file,filesize($fileName));
    $stringValue = str_replace("!!SecurityArchitects", "Security Architect", $stringValue);
    $stringValue = str_replace("!!ChiefInformationSecurityOfficer", "Chief Information Security Officer", $stringValue);
    $json = json_decode($stringValue);
    fclose($file);

    // Risk Profile
    $approvalFlow = new ApprovalFlow();
    $approvalFlow->name = "Default 2-Stage";
    $approvalFlow->details = json_encode($json);
    $approvalFlow->save(); 
  }
}

