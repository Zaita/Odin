<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-submission {uuid} {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the submission with the specified role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      $uuid = $this->argument('uuid');
      $role = $this->argument('role');

      $submission = Submission::where('uuid', $uuid)->first();
      $approvalFlow = json_decode($submission->approval_flow_data);
      $flow = json_decode($approvalFlow->details);

      foreach($flow->flow as $approvalStage) {
        foreach($approvalStage as $approver) {
          if ($approver->type == "group") {
            printf("Checking Approver: %s\n", $approver->group);
            if ($approver->group == $role) {
              printf("Matched - Updating Record\n");
              $approver->approval_status = "in_review";
              $approver->assigned_to = "Console";
            }
          } 
        }
      }
      $approvalFlow->details = json_encode($flow);
      $submission->approval_flow_data = json_encode($approvalFlow);
      $submission->save();
    }
}
