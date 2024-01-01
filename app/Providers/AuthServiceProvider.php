<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Policies\AdminPolicy;
use App\Models\Group;
use App\Models\GroupUser;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
      Gate::define('isAdmin', function ($user) {
        // Load our Administrators Group Id
        $group = Group::firstOrNew(["name" => "Administrators"]);
        if (is_null($group->id)) {
          return False; 
        }

        $groupId = $group->id;
        $userId = $user->id;
        return GroupUser::where(["user_id" => $userId, "group_id" => $groupId])->count() == 1;        
      });
    }
}

