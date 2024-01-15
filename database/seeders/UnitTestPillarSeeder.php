<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\ApprovalFlow;

class UnitTestPillarSeeder extends Seeder
{
  protected $sortOrder = 0;

  protected function load_pillar($name, $approvalFlow) {
    $fileName = "storage/content/testdata/pillars/${name}.json";
    $file = fopen($fileName, "r") or die("Unable to open file!");
    $json = json_decode(fread($file,filesize($fileName)), true);
    fclose($file);
    
    $approvalFlow = ApprovalFlow::where(['name' => $approvalFlow])->first();

    $p = Pillar::firstOrNew(["name" => $json["name"]]);
    $p->importFromJson($json);
    $p->sort_order = $this->sortOrder++;
    $p->approval_flow_id = $approvalFlow->id;
    $p->save();
  }

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->load_pillar("TestSingleStepApprovalFlow", "Security only");
    $this->load_pillar("TestTwoStepApprovalFlow", "Security and business owner only");
    $this->load_pillar("TestAllInputTypes", "Security and business owner only");
    $this->load_pillar("TestTwoStepApprovalWithCISO", "Two stage approval with CISO endorsement");
  }
}

