<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionSecurityControl extends Model
{
    use HasFactory;

    protected $fillable = [
      "submission_id",
      "sra_submission_id",
      "security_catalogue_name",
      "name",
      "description",
      "implementation_guidance",
      "implementation_evidence",
      "audit_guidance",
      "reference_standards",
      "control_owner_name",
      "control_owner_email",
      "tags"      
    ];
}
