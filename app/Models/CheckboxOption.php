<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckboxOption extends Model
{
    use HasFactory;

    protected $fillable = [
      'label',
      'value',
      'risks',
    ];
}
