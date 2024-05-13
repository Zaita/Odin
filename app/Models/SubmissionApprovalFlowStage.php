<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\ApprovalFlowStage;

class SubmissionApprovalFlowStage extends Model
{
    use HasFactory;

    protected $table = 'submission_approval_flow_stages';

    protected $fillable = [            
      'type',
      'target',
      'approval_type',
      'wait_for_approval',
      'stage_order',
      'assigned_to_user_id',
      'assigned_to_user_name',
      'assigned_to_user_email',
      'approved_by_user_id',
      'approved_by_user_name',
      'approved_by_user_emai',
      'status'
    ];

    protected $hidden = [
      'approval_flow_id',
    ];
    
  /**
   * Construct our submission with some default values
   */
  public function __construct(array $attributes = array()) {
    parent::__construct($attributes);
  }

  public function initAndSave(ApprovalFlowStage $stage, $submissionId) {
    $this->fill(json_decode(json_encode($stage), true));
    $this->submission_id = $submissionId;
    unset($this->id);
    unset($this->approval_flow_id);
    unset($this->enabled);
    $this->save();
  }
}
