<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;

class SubmissionCollaborator extends Model
{
    use HasFactory;

    protected $fillable = [
      'submission_id',
      'user_id',
  ];

  public function user(): BelongsTo
  {
      return $this->belongsTo(User::class);
  }
}
