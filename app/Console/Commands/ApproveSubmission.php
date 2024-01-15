<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Submission;
use App\Models\User;

class ApproveSubmission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:approve-submission {uuid} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve the submission as the specified role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $uuid = $this->argument('uuid');
        $email = $this->argument('email');

        $submission = Submission::where('uuid', $uuid)->first();
        $user = User::where(['email' => $email])->first();
        $submission->approve($user);        
    }
}
