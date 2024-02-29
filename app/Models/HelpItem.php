<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpItem extends Model
{
    use HasFactory;

    protected $fillable = [
      "name",
      "summary",
      "content",
      "sort_order",
    ];
}
