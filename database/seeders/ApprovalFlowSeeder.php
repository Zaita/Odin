<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\ApprovalFlow;
use App\Models\Group;

class ApprovalFlowSeeder extends Seeder
{
  protected function load_approval_flow($name) {
    $fileName = "storage/content/approvalFlows/$name.json";
    $file = fopen($fileName, "r") or die("Unable to open file!");
    $stringValue = fread($file,filesize($fileName));
    $stringValue = str_replace("!!SecurityArchitects", "Security Architect", $stringValue);
    $stringValue = str_replace("!!ChiefInformationSecurityOfficer", "Chief Information Security Officer", $stringValue);
    $json = json_decode($stringValue);
    fclose($file);

    $approvalFlow = new ApprovalFlow();
    $approvalFlow->importFromJson($json);
    $approvalFlow->save(); 
  }
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->load_approval_flow("two_stage_with_ciso_endorsement");
    $this->load_approval_flow("security_only");
    $this->load_approval_flow("security_and_business_owner_only");   
  }
}

