<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalFlowStage extends Model
{
    use HasFactory;

    protected $table = 'approval_flow_stages';

    protected $fillable = [      
      'stage_order',
      'type',
      'target',
      'approval_type',
      'wait_for_approval',
    ];

    protected $hidden = [
      'submission_id',
    ];


}
