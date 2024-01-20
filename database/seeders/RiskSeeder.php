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
    Risk::create(['name' => "Information Disclosure", "description" => "Information is disclosed"]);
    Risk::create(['name' => "Information Modification", "description" => "Information is modified"]);
    Risk::create(['name' => "Information Loss", "description" => "Information is lost"]);
  }
}

