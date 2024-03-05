<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

use App\Models\User;
use App\Models\Group;
use App\Models\Pillar;
use App\Models\Submission;
use App\Models\TaskSubmission;

class SubmissionTaskTest extends TestCase {
  private static $user = null;
  protected function setUp() : void {
    parent::setUp();

    // self::$user = User::where(['email' => 'user@zaita.com'])->first();
    // $this->security = User::where(['email' => 'sec@zaita.com'])->first();
  }

  public function test_users(): void {
    // $this->assertTrue(self::$user->email == "user@zaita.com");
    // $this->assertTrue($this->security->email == "sec@zaita.com");
  }

  /**
   * Test creating a submission and completing the created task
   * Then approve it as the Security Architect
   */
  public function test_submission_with_task() : void {
    // $id = Pillar::where(["name" => "Test Single Task Submission"])->first()->id;
    // $response = $this->actingAs(self::$user)->get("/start/${id}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //     ->component("Start")
    //     ->has("ziggy.location")
    //     ->has("siteConfig")
    //     ->has("pillar")
    //     ->where("pillar.id", $id)
    //     ->where("pillar.name", "Test Single Task Submission")
    // );

    // $response = $this->actingAs(self::$user)->post("/start/${id}");
    // $response->assertStatus(302);
    // $this->assertDatabaseCount('submissions', 1);

    // /**
    //  * Verify we can load the in_progress screen
    //  */
    // $submission = Submission::first();
    // $this->assertModelExists($submission);
    // $uuid = $submission->uuid;

    // $response = $this->actingAs(self::$user)->get("/inprogress/${uuid}");
    // $response->assertStatus(200);

    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/InProgress")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->url("/inprogress/${uuid}")
    // );

    // /**
    //  * Answer the question:
    //  * Two fields, Product Name and Business Owner
    //  */
    // $answers = array();
    // $answers["answers"] = array();
    // $answers["answers"]["Product Name"] = "Product ABC";
    // $answers["answers"]["Business Owner"] = "bo@zaita.com";
    // $answers["question"] = "Q1";
    
    // $response = $this->actingAs(self::$user)->post("/inprogress/${uuid}", $answers);

    // // Verify we've landed on the review screen
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.review", $uuid);

    // // Submit the review page
    // $response = $this->actingAs(self::$user)->post("/submit/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // // View the page as the submitter to verify there is a task available
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Tasks to complete')
    //   ->has('tasks', 1)
    //   ->where('tasks.0.name', 'One Question Task')
    //   ->where('tasks.0.status', 'ready_to_start')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Try, but fail to submit the submission
    // $response = $this->actingAs(self::$user)->post("/submitforapproval/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 1)
    //   ->where('errors.error', 'Not all tasks have been completed')
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Tasks to complete')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // /**
    //  * Get the UUID of the task
    //  */
    // $task = TaskSubmission::where(['submission_id' => $submission->id])->first();
    // $this->assertTrue($task != null);
    // $tuuid = $task->uuid;

    // // Click on task
    // $response = $this->actingAs(self::$user)->get("/task/${tuuid}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Task/Information")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->where('task.name', 'One Question Task')
    //   ->url("/task/${tuuid}")
    // );

    // // Start task
    // $response = $this->actingAs(self::$user)->post("/task/start/${tuuid}");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.task.inprogress", $tuuid);

    // // Answer the question verify we've gone to review
    // $answers = array();
    // $answers["answers"] = array();
    // $answers["answers"]["action"] = "continue";
    // $answers["question"] = "Information";
    
    // $response = $this->actingAs(self::$user)->post("/task/inprogress/${tuuid}", $answers);
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.task.review", $tuuid);

    // // Submit the task from review to done.
    // $response = $this->actingAs(self::$user)->post("/task/submit/${tuuid}");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // // View the page as the submitter and now it should have completed task and be ready to submit
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Ready to submit')
    //   ->has('tasks', 1)
    //   ->where('tasks.0.name', 'One Question Task')
    //   ->where('tasks.0.status', 'complete')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Post the submission for approval
    // $response = $this->actingAs(self::$user)->post("/submitforapproval/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'waiting_for_approval')
    //   ->where('submission.nice_status', 'Waiting for approval')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // View the page as a security architect to verify assign_to_me functionality
    // $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'waiting_for_approval')
    //   ->where('submission.nice_status', 'Waiting for approval')      
    //   ->where('is_an_approver', true)
    //   ->where('can_be_assigned', true)
    //   ->where('can_approve_with_type', false)
    // );

    // // Assign the submission to the security architect
    // $response = $this->actingAs($this->security)->post("/assigntome/${uuid}");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'waiting_for_approval')
    //   ->where('submission.nice_status', 'Waiting for approval')      
    //   ->where('is_an_approver', true)
    //   ->where('can_be_assigned', false) // Already assigned
    //   ->where('can_approve_with_type', 'approval')
    // );

    // // Approve the submission
    // $response = $this->actingAs($this->security)->post("/approve/${uuid}");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'approved')
    //   ->where('submission.nice_status', 'Approved')      
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false) // Already assigned
    //   ->where('can_approve_with_type', false)
    // );
  }
}
