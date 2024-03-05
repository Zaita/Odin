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

class ApprovalFailureModesTest extends TestCase {
  // Load our Seeds

  private static $user = null;
  protected function setUp() : void {
    parent::setUp();

    // self::$user = User::where(['email' => 'user@zaita.com'])->first();
    // $this->security = User::where(['email' => 'sec@zaita.com'])->first();
    // $this->business = User::where(['email' => 'bo@zaita.com'])->first();
    // $this->ciso = User::where(['email' => 'ciso@zaita.com'])->first();
  }

  public function test_users(): void {
    // $this->assertTrue(self::$user->email == "user@zaita.com");
    // $this->assertTrue($this->security->email == "sec@zaita.com");
    // $this->assertTrue($this->business->email == "bo@zaita.com");
    // $this->assertTrue($this->ciso->email == "ciso@zaita.com");
  }

  public function test_multiple_submissions() : void {
    // $id = Pillar::where(["name" => "Test Single-Step Approval Flow"])->first()->id;
    // $response = $this->actingAs(self::$user)->get("/start/${id}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //     ->component("Start")
    //     ->has("ziggy.location")
    //     ->has("siteConfig")
    //     ->has("pillar")
    //     ->where("pillar.id", $id)
    //     ->where("pillar.name", "Test Single-Step Approval Flow")
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

    // // Submit the review page again
    // // This won't do anything.
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response = $this->actingAs(self::$user)->post("/submit/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasNoErrors();

    // // View the page as the submitter to verify they can't approve and the error is present
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Ready to submit')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Verify the error is no longer there
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Ready to submit')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Submit the submission for approval
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

    // // Try to submit for approval again and verify error
    // $response = $this->actingAs(self::$user)->post("/submitforapproval/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasErrors([
    //   'error' => 'Submission has already been sent for approval'
    // ]);

    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 1)
    //   ->where("errors.error", 'Submission has already been sent for approval')
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'waiting_for_approval')
    //   ->where('submission.nice_status', 'Waiting for approval')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
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

    // // Try to approve the submission again
    // $response = $this->actingAs($this->security)->post("/approve/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasErrors([
    //   'error' => 'Cannot approve a submission that has been fully approved or denied'
    // ]);

    // $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 1)
    //   ->where('errors.error', 'Cannot approve a submission that has been fully approved or denied')
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'approved')
    //   ->where('submission.nice_status', 'Approved')      
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false) // Already assigned
    //   ->where('can_approve_with_type', false)
    // );
  }

  /***********************************************************************************************
   * Test creating a submission but having someone other than the submitter trying to move
   * it through the stages; and then the submitter trying to approve their own submission
   * *********************************************************************************************
   */
  public function test_non_submitter_trying_to_submit() : void {
    // $id = Pillar::where(["name" => "Test Single-Step Approval Flow"])->first()->id;
    // $response = $this->actingAs(self::$user)->get("/start/${id}");
    // $response->assertStatus(200);
    // $response->assertInertia(fn (Assert $page) => $page
    //     ->component("Start")
    //     ->has("ziggy.location")
    //     ->has("siteConfig")
    //     ->has("pillar")
    //     ->where("pillar.id", $id)
    //     ->where("pillar.name", "Test Single-Step Approval Flow")
    // );

    // $response = $this->actingAs(self::$user)->post("/start/${id}");
    // $response->assertStatus(302);
    // $this->assertDatabaseCount('submissions', 1);

    // // Load in_progress screen as user
    // $submission = Submission::first();
    // $this->assertModelExists($submission);
    // $uuid = $submission->uuid;

    // $response = $this->actingAs(self::$user)->get("/inprogress/${uuid}");
    // $response->assertStatus(200);

    // // Answer for question
    // $answers = array();
    // $answers["answers"] = array();
    // $answers["answers"]["Product Name"] = "Product ABC";
    // $answers["answers"]["Business Owner"] = "bo@zaita.com";
    // $answers["question"] = "Q1";
    
    // // Try to answer as someone else
    // $response = $this->actingAs($this->business)->post("/inprogress/${uuid}", $answers);
    // $response->assertRedirectToRoute("error");
    // $response->assertSessionHasErrors([
    //   'error' => 'Could not find that submission'
    // ]);

    // // Now answer as submitter
    // $response = $this->actingAs(self::$user)->post("/inprogress/${uuid}", $answers);

    // // Verify we've landed on the review screen
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.review", $uuid);

    // // Try to submit as someone else
    // $response = $this->actingAs($this->ciso)->post("/submit/${uuid}");
    // $response->assertRedirectToRoute("error");
    // $response->assertSessionHasErrors([
    //   'error' => 'Could not find that submission'
    // ]);    

    // // Now submit as the submitter
    // $response = $this->actingAs(self::$user)->post("/submit/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
 
    // // View the page as the submitter to verify they can't approve and the error is present
    // $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/${uuid}")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Ready to submit')
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Submit the submission for approval as someone else
    // $response = $this->actingAs($this->security)->post("/submitforapproval/${uuid}");
    // $response->assertRedirectToRoute("error");
    // $response->assertSessionHasErrors([
    //   'error' => 'Could not find that submission'
    // ]);   

    // // Submit the submission for approval as submitter
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
    
    // // Verify no errors
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

    // // Try to assign to the submission to someone who shouldn't
    // $response = $this->actingAs($this->ciso)->post("/assigntome/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasErrors([
    //   'error' => 'You do not have permission to assign this submission to yourself'
    // ]);   

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

    // // Try to approve the submission as someone who shouldn't
    // $response = $this->actingAs($this->ciso)->post("/approve/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasErrors([
    //   'error' => 'You do not have permission to approve this submission'
    // ]);  

    // // Try to approve the submission as the submitter
    // $response = $this->actingAs(self::$user)->post("/approve/${uuid}");
    // $response->assertRedirectToRoute("submission.submitted", $uuid);
    // $response->assertSessionHasErrors([
    //   'error' => 'You cannot approve your own submissions'
    // ]);  

    // // Approve the submission as the security architect
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
