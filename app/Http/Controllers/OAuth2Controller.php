<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class OAuth2Controller extends Controller
{
  public function redirectToIdp()
  {
     return Socialite::driver('okta')->redirect();
  }
  
  public function handleIdpCallback()
  {
    //  $user = Socialite::driver('okta')->stateless()->user();
     $user = Socialite::driver('okta')->user();
    
     Log::Info($user->email);
     $localUser = User::where('email', $user->email)->first();
      Log::Info($localUser);

     if (!$localUser) {
      $localUser = new User();
      $localUser->name = $user->name;
      $localUser->email = $user->email;
      $localUser->sso = true;
      $localUser->token = $user->token;
      $localUser->save();
     } else {
      $localUser->sso = true;
      $localUser->token = $user->token;
      $localUser->save();
     }

     Auth::login($localUser, true);

     AuditLog::LogUserAction("Okta.User.Login", $user);
     return redirect('/');
     // Check if the user exists in your system based on their email or other unique identifier.
     // If not, create a new user account.
     // Log in the user using JWT or other authentication method.
  }
}
