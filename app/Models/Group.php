<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\GroupUser;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
      'name',
      'description'      
    ];

    protected $appends = [
      'users'      
    ];

      /**
     * Bind simple attribute for Model to use.
     */
    protected function Users() : Attribute {
      return Attribute::make(
        get: fn (null $value) => GroupUser::where('group_id', $this->id)->count(),
      );
    }

    /**
     * Add a user to the current group, but only if they haven't been 
     * added before
     */
    public function AddUser($userId) {
      GroupUser::firstOrCreate([
        'user_id' => $userId,
        'group_id' => $this->id
      ]);
    }
}
