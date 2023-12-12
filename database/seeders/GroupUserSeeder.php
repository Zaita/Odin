<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Group;
use App\Models\User;
use App\Models\GroupUser;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $admin = User::where('email', 'admin@zaita.com')->first();
      $adminGrp = Group::where('name', 'Administrator')->first();

      GroupUser::create(['user_id' => $admin->id, 'group_id' => $adminGrp->id]);
    }
}
