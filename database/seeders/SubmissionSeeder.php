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
      $user = User::where('email', 'admin@zaita.com')->first();

      $pillar = Pillar::where("name", "Proof of Concept")->first();     
      $questionnaire = Questionnaire::with([
        "questions" => function(Builder $q) {$q->orderBy('sort_order');},
        "questions.inputFields",
        "questions.actionFields",
        ])->findOrFail($pillar->questionnaire_id);

      $approvalFlow = ApprovalFlow::findOrFail($pillar->approval_flow_id);

      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->user_id = $user->id;
        $s->submitter_name = $user->name;
        $s->submitter_email = $user->email;
        $s->pillar_name = $pillar->name;
        $s->product_name = sprintf("Product %d", $i);
        $s->pillar_data = $pillar;
        $s->approval_flow_data = $approvalFlow;
        $s->questionnaire_data = $questionnaire;        
        $s->save();
      }

      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->user_id = $user->id;
        $s->submitter_name = $user->name;
        $s->submitter_email = $user->email;
        $s->pillar_name = $pillar->name;
        $s->product_name = sprintf("Product %d", $i);
        $s->pillar_data = $pillar;
        $s->approval_flow_data = $approvalFlow;
        $s->questionnaire_data = $questionnaire;
        $s->status = "submitted";
        $s->save();
      }

      for ($i = 0; $i < 5; $i++) {
        $s = new Submission();
        $s->user_id = $user->id;
        $s->submitter_name = $user->name;
        $s->submitter_email = $user->email;
        $s->pillar_name = $pillar->name;
        $s->product_name = sprintf("Product %d", $i);
        $s->pillar_data = $pillar;
        $s->approval_flow_data = $approvalFlow;
        $s->questionnaire_data = $questionnaire;
        $s->status = "approved";
        $s->save();
      }
    }
}

