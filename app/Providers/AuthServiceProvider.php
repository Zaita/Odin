<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;

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

    protected function isUserInAnyGroup($user, array $groups) {
      foreach( $groups as $group ) {
        if ($user->isInGroup($group)) {
          return true;
        }
      }

      return false;
    }

    /**
     * Register any authentication / authorization services.
     * 
     * - Administrator
     * - Read Only Administrator
     * - Content Administrator
     * - Audit Log Viewer
     * - Report Viewer
     */
    public function boot(): void
    {
      Gate::define('isAdministrator', function ($user) {
        return $this->isUserInAnyGroup($user, ["Administrator"]);       
      });

      Gate::define('isReadOnlyAdministrator', function($user) {
        return $this->isUserInAnyGroup($user, ["Read Only Administrator", "Content Administrator", "Administrator"]);
      });

      Gate::define('isContentAdministrator', function($user) {
        return $this->isUserInAnyGroup($user, ["Content Administrator", "Administrator"]);
      });

      Gate::define('isAuditLogViewer', function($user) {
        return $this->isUserInAnyGroup($user, ["Audit Log Viewer", "Read Only Administrator", "Administrator"]);
      });

      Gate::define('isReportViewer', function($user) {
        return $this->isUserInAnyGroup($user, ["Report Viewer", "Read Only Administrator", "Administrator",]);
      });
      
      Gate::define('isAnyAdmin', function($user) {
        return $this->isUserInAnyGroup($user, ["Administrator", "Read Only Administrator",
        "Content Administrator", "Audit Log Viewer", "Report Viewer"]);
      });

      Gate::define('block', function($user) {
        return false;
      });
      
    }
}

