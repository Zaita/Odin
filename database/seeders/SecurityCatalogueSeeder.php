<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\SecurityCatalogue;

class SecurityCatalogueSeeder extends Seeder
{

  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $name = "DefaultSecurityCatalogue";
    $fileName = "storage/content/testdata/$name.json";
    $file = fopen($fileName, "r") or die("Unable to open file!");
    $json = json_decode(fread($file,filesize($fileName)), true);
    fclose($file);

    $sc = SecurityCatalogue::firstOrNew(["name" => $json["name"]]);
    $sc->importFromJson($json);
    $sc->save();

  }
}

