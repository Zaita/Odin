<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionField extends Model
{
    use HasFactory;

    protected $table = 'action_fields';
    
    protected $fillable = [
      'question_id',
      'label',
      'action_type',
      'goto_question_title',
      'tasks',       
    ];


}
