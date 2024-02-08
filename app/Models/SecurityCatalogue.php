<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\SecurityControl;


class SecurityCatalogue extends Model
{
    use HasFactory;

    protected $fillable = [
      "name",
      "description"
    ];

    public function security_controls(): HasMany {
      return $this->hasMany(SecurityControl::class);
    }
}
