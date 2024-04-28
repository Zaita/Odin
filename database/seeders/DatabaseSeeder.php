<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ApprovalFlowSeeder;
use Database\Seeders\ConfigurationSeeder;
use Database\Seeders\PillarSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\GroupUserSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\RiskSeeder;
use Database\Seeders\ReportSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\SecurityCatalogueSeeder;
use Database\Seeders\ImpactThresholdSeeder;
use Database\Seeders\LikelihoodThresholdSeeder;
use Database\Seeders\HelpItemSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $this->call([
        UserSeeder::class,
        ConfigurationSeeder::class,
        HelpItemSeeder::class,        
        GroupSeeder::class,
        GroupUserSeeder::class,
        ApprovalFlowSeeder::class,
        RiskSeeder::class,        
        SecurityCatalogueSeeder::class,        
        ImpactThresholdSeeder::class,  
        LikelihoodThresholdSeeder::class,      
        ReportSeeder::class,
        TaskSeeder::class,
        PillarSeeder::class,
        SubmissionSeeder::class,        
      ]);
    }
}
