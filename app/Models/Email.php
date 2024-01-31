<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\EmailMainRequest;

class Email extends Model
{

  use HasFactory;
  public $error = null;

  public function updateMainSettings(EmailMainRequest $request) 
  {
    Log::Info("Updating Email Main Settings");
    $alternateHostname = $request->input('alternate_hostname_for_email', null);
    if ($alternateHostname != null) {

    }

    $emailFromAddress = $request->input('email_from_address', null);
    if ($emailFromAddress != null) {
      if (filter_var($emailFromAddress, FILTER_VALIDATE_EMAIL) === false) {
        $this->error = "Email from address must be a valid email address";
        return false;
      }
    }

    
  
  }
}
