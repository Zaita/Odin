<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LikelihoodThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
      "name",
      "color",
      "operator",
      "value"
    ];

    public static function all_as_json_str() : mixed {
      return "{}";
    }
}
