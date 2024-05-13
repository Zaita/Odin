<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = "audit_logs";

    protected $fillable = [
      'action',
      'user_name',
      'user_email',
      'request'     
  ];

    public static function Log($action, $request) {
      $user = $request->user();
      Log::Info("$action");
      AuditLog::create(
        [
        'action' => $action,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'request' => json_encode($request->except(["password"]))
        ]
      );
    }

    public static function LogUserAction($action, $user) {
      Log::Info("$action");
      AuditLog::create(
        [
        'action' => $action,
        'user_name' => $user->name,
        'user_email' => $user->email,
        'request' => '{}'
        ]
      );
    }
}
