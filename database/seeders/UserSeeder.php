<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\ApprovalFlow;
use App\Models\Group;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    // \App\Models\User::factory()->create([
    //   'name' => 'Admin Demonstration',
    //   'email' => 'admin@zaita.io',
    //   'password' => bcrypt('jJ9tm$1Oo80aQ%jk'),
    // ]);

    // \App\Models\User::factory()->create([
    //   'name' => 'Security Architect Demonstration',
    //   'email' => 'security@zaita.io',
    //   'password' => bcrypt('7fCq%DEvc3145HOA'),
    // ]);

    // \App\Models\User::factory()->create([
    //   'name' => 'User Demonstration',
    //   'email' => 'user@zaita.io',
    //   'password' => bcrypt('4qH^O!!yNadMASbP'),
    // ]);

    // \App\Models\User::factory()->create([
    //   'name' => 'CISO Demonstration',
    //   'email' => 'ciso@zaita.io',
    //   'password' => bcrypt('Chrg5#ERAdBPMOFr'),
    // ]);

    // \App\Models\User::factory()->create([
    //   'name' => 'Business Owner Demonstration',
    //   'email' => 'bo@zaita.io',
    //   'password' => bcrypt('uHB8sQ@OmdkBCDTa'),
    // ]);

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
      'email' => 'security@zaita.com',
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

    \App\Models\User::factory(10)->create();
  }
}

