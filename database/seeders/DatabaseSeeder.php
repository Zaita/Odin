<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ApprovalFlowSeeder;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\PillarSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\GroupUserSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\SubmissionSeeder;
use Database\Seeders\RiskSeeder;

class DatabaseSeeder extends Seeder
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
        'password' => bcrypt('user'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott Collab',
        'email' => 'collab@zaita.com',
        'password' => bcrypt('collab'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott SecArch',
        'email' => 'sec@zaita.com',
        'password' => bcrypt('sec'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott CISO',
        'email' => 'ciso@zaita.com',
        'password' => bcrypt('ciso'),
      ]);

      \App\Models\User::factory()->create([
        'name' => 'Scott BO',
        'email' => 'bo@zaita.com',
        'password' => bcrypt('bo'),
      ]);


      \App\Models\User::factory(50)->create();

      $this->call([
        GroupSeeder::class,
        GroupUserSeeder::class,
        ApprovalFlowSeeder::class,
        RiskSeeder::class,
        PillarSeeder::class,
        TaskSeeder::class,
        SubmissionSeeder::class
      ]);
    }
}
