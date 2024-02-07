<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityControl extends Model
{
    use HasFactory;
    protected $fillable = [
        "name", "description", 
        "implementation_guidance", 
        "implementation_evidence", 
        "audit_guidance", 
        "reference_standards", 
        "control_owner_name",
        "control_owner_email", 
        "tags",
    ];

    public function updateRisks(array $inputs) {

    }
}
