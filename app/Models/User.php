<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
      'groups_string'      
    ];

   /**
   * Bind simple attribute for Model to use.
   */
  // protected function Groups() : Attribute {
  //   return Attribute::make(
  //     get: fn (null $value) => $this->groupMembership(),
  //   );
  // }

   /**
   * Bind simple attribute for Model to use.
   */
  protected function GroupsString() : Attribute {
    return Attribute::make(
      get: fn (null $value) => $this->getGroupList(),
    );
  }

  public function getGroupList() {
    $results = array();
    foreach ($this->groupMembership as $group) {
      array_push($results, $group->name);
    }

    return count($results) > 0 ? join(", ", $results) : "-";
  }

  public function groupMembership(): BelongsToMany
  {
      return $this->belongsToMany(Group::class);
  }

  public function isInGroup($groupName) {
    foreach ($this->groupMembership as $group) {
      if ($groupName == $group->name) {
        return true;
      }
    }
    return false;
  }
}
