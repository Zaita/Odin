<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Risk;

class SecurityControlRiskWeight extends Model
{
    use HasFactory;

    protected $fillable = [
      "name",
      "likelihood", 
      "likelihood_penalty",
      "impact",
      "impact_penalty",
    ];   

    public function risk(): BelongsTo {
      return $this->BelongsTo(Risk::class);
    }
}
