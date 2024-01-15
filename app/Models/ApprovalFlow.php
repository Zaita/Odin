<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

use App\Models\ApprovalFlowStage;

class ApprovalFlow extends Model
{
  use HasFactory;
  
  protected $table = 'approval_flows';

  protected $fillable = [
      'name',
      'enabled',        
  ];

  // Define the relationship to approval_flow_stages table in Database
  public function stages(): HasMany {
    return $this->hasMany(ApprovalFlowStage::class);
  }

  /**
   * Import a new Approval Flow configuration from a JSON file
   */
  public function importFromJson($json) {
    $this->name = $json->name;
    $this->save();
    
    $stageOrder = 0;
    foreach($json->stages as $stage) {
      $jsonArr = json_decode(json_encode($stage), true);
      $stage = new ApprovalFlowStage();
      $stage->fill($jsonArr);
      $stage->stage_order = $stageOrder++;
      $stage->approval_flow_id = $this->id;
      $stage->save();
    }
  }
}
