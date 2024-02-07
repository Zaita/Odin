<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;
use Database\Seeders\ApprovalFlowSeeder;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\PillarSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\GroupUserSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\RiskSeeder;
use Database\Seeders\ReportSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SecurityCatalogueSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $this->call([
        UserSeeder::class,
        GroupSeeder::class,
        GroupUserSeeder::class,
        ApprovalFlowSeeder::class,
        RiskSeeder::class,
        PillarSeeder::class,
        TaskSeeder::class,
        SubmissionSeeder::class,
        ReportSeeder::class,
        SecurityCatalogueSeeder::class,
      ]);
    }
}
