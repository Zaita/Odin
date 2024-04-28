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
  protected function addToGroup($u, $g) {
    $admin = User::where('email', $u)->first();
    $adminGrp = Group::where('name', $g)->first();
    GroupUser::create(['user_id' => $admin->id, 'group_id' => $adminGrp->id]);
  }
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $this->addToGroup("admin@zaita.io", "Administrator");
    $this->addToGroup("security@zaita.io", "Security Architect");
    $this->addToGroup("ciso@zaita.io", "Chief Information Security Officer");
  }
}
