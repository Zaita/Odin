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

    /**
     * Import our security catalogue from a JSON file.
     */
    public function importFromJson(array $jsonArr) {
      // Strip out everything not relevant and update current object
      $relevantJson = array_filter($jsonArr, function($k) { 
        return in_array($k, $this->fillable);
      }, ARRAY_FILTER_USE_KEY);
      $this->fill($relevantJson);  
      $this->save();

      // Add our Security Controls now
      foreach($jsonArr["security_controls"] as $securityControl) {
        $sc = SecurityControl::firstOrNew(["name" => $securityControl["name"], "security_catalogue_id" => $this->id]);
        $sc->security_catalogue_id = $this->id;
        $sc->importFromJson($securityControl);
      }
    }
}
