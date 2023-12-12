<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Group;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $record = Group::create(
        ['name' => "Administrator",
        'description' => "Full access to the SDLT"
        ]
      );
      $record = Group::create(
        ['name' => "Security Architect",
        'description' => "First approvers for each submission"
        ]
      );
      $record = Group::create(
        ['name' => "Chief Information Security Officer",
        'description' => "Chief Information Security Officer Approver"
        ]
      );
    }
}
