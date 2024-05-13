<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Eloquent\Builder;

use App\Models\ApprovalFlow;
use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\Submission;
use App\Models\User;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Submission::where('id', '>', '0')->update(['status' => 'expired']);

      $user = User::where('email', 'user@zaita.com')->first();

      $pillar = Pillar::findOrFail(2);      
      $questionnaire = Questionnaire::with([
        "questions" => function(Builder $q) {$q->orderBy('sort_order');},
        "questions.inputFields",
        "questions.inputFields.input_options",
        "questions.actionFields"      
        ])->findOrFail($pillar->questionnaire_id);
  
      $approvalFlow = ApprovalFlow::findOrFail($pillar->approval_flow_id);
      $user = User::where('email', 'user@zaita.com')->first();
      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->initAndSave($pillar, $user, $questionnaire);
        $s->product_name = sprintf("Product %d", $i);        
        $s->save();
      }

      $user = User::where('email', 'usertwo@zaita.com')->first();
      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->initAndSave($pillar, $user, $questionnaire);        
        $s->product_name = sprintf("Product %d", $i);
        $s->status = "submitted";
        $s->save();
      }

      $user = User::where('email', 'userthree@zaita.com')->first();
      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->initAndSave($pillar, $user, $questionnaire);        
        $s->product_name = sprintf("Product %d", $i);
        $s->status = "waiting_for_approval";
        $s->save();
      }
    }
}

