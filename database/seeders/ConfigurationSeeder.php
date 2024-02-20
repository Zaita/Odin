<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Group;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $fileName = "storage/content/configuration/dashboard.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      DB::table("configurations")->insert(['label' => 'dashboard', 'value' => json_encode($json)]);
    }
}
