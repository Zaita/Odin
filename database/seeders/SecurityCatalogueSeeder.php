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
    SecurityCatalogue::create(['name' => 'Default Control Catalogue', 'description' => 'In-Built default control catalogue based on NZISM and NIST']);
  }
}

