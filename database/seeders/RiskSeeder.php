<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Risk;

class RiskSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void {
    Risk::create(['name' => "Information disclosure", "description" => "Information is disclosed"]);
    Risk::create(['name' => "Information modification", "description" => "Information is modified"]);
    Risk::create(['name' => "Information loss", "description" => "Information is lost"]);
    Risk::create(['name' => "Degraded service performance", "description" => "DSP"]);
    Risk::create(['name' => "Sustained service unavailability", "description" => "SSA"]);
    Risk::create(['name' => "Harm to, or loss of human life", "description" => "MDK"]);
  }
}

