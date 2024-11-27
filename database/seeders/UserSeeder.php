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
    \App\Models\User::factory()->create([
      'name' => 'Demo Admin User',
      'email' => 'admin@zaita.io',
      'password' => bcrypt('jJ9tm$1Oo80aQ%jk'),
    ]);

    \App\Models\User::factory()->create([
      'name' => 'Demo Security User',
      'email' => 'security@zaita.io',
      'password' => bcrypt('7fCq%DEvc3145HOA'),
    ]);

    \App\Models\User::factory()->create([
      'name' => 'Demon CISO User',
      'email' => 'ciso@zaita.io',
      'password' => bcrypt('Chrg5#ERAdBPMOFr'),
    ]);

    \App\Models\User::factory()->create([
      'name' => 'Demo User One',
      'email' => 'user@zaita.io',
      'password' => bcrypt('Rizt0Ty%XRNY2l2c'),
    ]);

    \App\Models\User::factory()->create([
      'name' => 'Demo User Two',
      'email' => 'usertwo@zaita.io',
      'password' => bcrypt('@6C8B9Y5oxIFsmaq'),
    ]);    

    \App\Models\User::factory()->create([
      'name' => 'Demo User Three',
      'email' => 'userthree@zaita.io',
      'password' => bcrypt('m3XRG%WC2UGwoLRj'),
    ]);  

    \App\Models\User::factory()->create([
      'name' => 'Demo Bob Collaborator',
      'email' => 'collab@zaita.io',
      'password' => bcrypt('eKd2^Yf!v*M#rs^V'),
    ]);

    \App\Models\User::factory()->create([
      'name' => 'Demo Joe Manager',
      'email' => 'bo@zaita.io',
      'password' => bcrypt('wX5@Mz5226nj82Yn'),
    ]);

    // \App\Models\User::factory(10)->create();
  }
}

