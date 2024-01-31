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
        'description' => "Full access to Odin"
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
      $record = Group::create(
        ['name' => "Read Only Admin",
        'description' => "Admin who can read, but not modify any content"
        ]
      );
      $record = Group::create(
        ['name' => "Content Administrator",
        'description' => "Administrator who can modify content (pillars, questionnaires, tasks). Cannot modify approval flows"
        ]
      );
      $record = Group::create(
        ['name' => "Report Viewer",
        'description' => "Can log in to admin portal and view reports only"
        ]
      );
      $record = Group::create(
        ['name' => "Audit Log Viewer",
        'description' => "Can log in to admin portal and view audit logs"
        ]
      );
    }
}
