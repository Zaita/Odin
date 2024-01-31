<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Report;
use App\Models\ApprovalFlow;

class ReportSeeder extends Seeder
{

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Report::create(["name" => "Number of non-expired submissions in each pillar"]);
    Report::create(["name" => "Number of submissions in each pillar per year"]);
    Report::create(["name" => "Number of submissions in each pillar per month and year"]);
    Report::create(["name" => "Number of submissions approved by each member of SecurityArchitects group"]);
    Report::create(["name" => "Number of submissions approved by each member of SecurityArchitects group per year and pillar"]);
    Report::create(["name" => "Number of submissions approved by each member of SecurityArchitects group per month/year and pillar"]);
    Report::create(["name" => "Number of tasks completed per year by type"]);
    Report::create(["name" => "Number of days between waiting for approval and approved"]);
    Report::create(["name" => "Number of days between starting a submission and and approved"]);
  }
}

