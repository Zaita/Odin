<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ApprovalFlowSeeder;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\UnitTestPillarSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\GroupUserSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\SubmissionSeeder;

class UnitTestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      \App\Models\User::factory()->create([
        'name' => 'admin',
        'email' => 'admin@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott User',
        'email' => 'user@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott Collab',
        'email' => 'collab@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott Architect',
        'email' => 'sec@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott CISO',
        'email' => 'ciso@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott Bus Owner',
        'email' => 'bo@zaita.com',
        'password' => bcrypt('admin'),
      ]);

      $this->call([
        GroupSeeder::class,
        GroupUserSeeder::class,
        ApprovalFlowSeeder::class,
        UnitTestPillarSeeder::class,
        TaskSeeder::class,
      ]);
    }
}
