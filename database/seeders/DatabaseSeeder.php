<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\QuestionnaireSeeder;
use Database\Seeders\PillarSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\GroupUserSeeder;

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

      \App\Models\User::factory(50)->create();

      $this->call([
        // QuestionnaireSeeder::class,
        PillarSeeder::class,
        GroupSeeder::class,
        GroupUserSeeder::class
      ]);
    }
}
