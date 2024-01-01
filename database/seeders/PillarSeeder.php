<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Pillar;
use App\Models\Questionnaire;

class PillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $sortOrder = 0;

      // Risk Profile
      $fileName = "storage/content/pillars/RiskProfile.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      $p = Pillar::firstOrNew(["name" => $json["name"]]);
      $p->importFromJson($json);
      $p->sort_order = $sortOrder++;
      $p->save();

      // Proof of Concept
      $fileName = "storage/content/pillars/ProofOfConcept.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      $p = Pillar::firstOrNew(["name" => $json["name"]]);
      $p->importFromJson($json);
      $p->sort_order = $sortOrder++;
      $p->save();
      
      // Cloud Product Onboarding
      $fileName = "storage/content/pillars/CloudProductOnboarding.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      $p = Pillar::firstOrNew(["name" => $json["name"]]);
      $p->importFromJson($json);
      $p->sort_order = $sortOrder++;
      $p->save();

      // New Project or Product
      $fileName = "storage/content/pillars/NewProjectOrProduct.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      $p = Pillar::firstOrNew(["name" => $json["name"]]);
      $p->importFromJson($json);
      $p->sort_order = $sortOrder++;
      $p->save();

      // Product Release
      $fileName = "storage/content/pillars/ProductRelease.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)), true);
      fclose($file);

      $p = Pillar::firstOrNew(["name" => $json["name"]]);
      $p->importFromJson($json);
      $p->sort_order = $sortOrder++;
      $p->save();      
    }
}

