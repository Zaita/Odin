<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\LikelihoodThreshold;

class LikelihoodThresholdSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void {
    LikelihoodThreshold::create(["name" => "Rare", "color" => "#00AB00", "operator" => "<", "value" => 5, "sort_order" => 1]);
    LikelihoodThreshold::create(["name" => "Unlikely", "color" => "#00AB00", "operator" => "<", "value" => 20, "sort_order" => 2]);
    LikelihoodThreshold::create(["name" => "Possible", "color" => "#FF8400", "operator" => "<", "value" => 40, "sort_order" => 3]);
    LikelihoodThreshold::create(["name" => "Likely", "color" => "#FA0000", "operator" => "<", "value" => 60, "sort_order" => 4]);
    LikelihoodThreshold::create(["name" => "Almost Certain", "color" => "#AD1818", "operator" => ">=", "value" => 60, "sort_order" => 5]);
  }
}

