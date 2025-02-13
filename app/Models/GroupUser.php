<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupUser extends Model
{
    use HasFactory;

    protected $table = 'group_user';

    protected $fillable = [
      'user_id',
      'group_id',
  ];

  
}
