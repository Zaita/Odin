<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\ImpactThreshold;

class ImpactThresholdSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void {
    ImpactThreshold::create(["name" => "Insignificant", "color" => "#00AB00", "operator" => "<", "value" => 10, "sort_order" => 1]);
    ImpactThreshold::create(["name" => "Minor", "color" => "#00AB00", "operator" => "<", "value" => 30, "sort_order" => 2]);
    ImpactThreshold::create(["name" => "Moderate", "color" => "#FF8400", "operator" => "<", "value" => 70, "sort_order" => 3]);
    ImpactThreshold::create(["name" => "Severe", "color" => "#FA0000", "operator" => "<", "value" => 130, "sort_order" => 4]);
    ImpactThreshold::create(["name" => "Extreme", "color" => "#AD1818", "operator" => ">=", "value" => 130, "sort_order" => 5]);
  }
}

