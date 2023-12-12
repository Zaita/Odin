<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
