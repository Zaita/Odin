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

class ApprovalFlowTwoStepTest extends TestCase {
  // Load our Seeds
  use RefreshDatabase;

  private static $user = null;
  protected function setUp() : void {
    parent::setUp();

    self::$user = User::where(['email' => 'user@zaita.com'])->first();
    $this->security = User::where(['email' => 'sec@zaita.com'])->first();
    $this->business = User::where(['email' => 'bo@zaita.com'])->first();
    $this->ciso = User::where(['email' => 'ciso@zaita.com'])->first();
  }

  /**
   * Test that we've created the users successfully
   */
  public function test_users(): void {
    $this->assertTrue(self::$user->email == "user@zaita.com");
    $this->assertTrue($this->security->email == "sec@zaita.com");
    $this->assertTrue($this->business->email == "bo@zaita.com");
    $this->assertTrue($this->ciso->email == "ciso@zaita.com");
  }

  /**
   * Test creating, completing and approving our submission.
   * Note: We do it in a single test because the RefreshDatabase wipes
   * the database between tests
   */
  public function test_submission() : void {
    $id = Pillar::where(["name" => "Test Two Step Approval Flow"])->first()->id;
    $response = $this->actingAs(self::$user)->get("/start/${id}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $id)
        ->where("pillar.name", "Test Two Step Approval Flow")
    );

    $response = $this->actingAs(self::$user)->post("/start/${id}");
    $response->assertStatus(302);
    $this->assertDatabaseCount('submissions', 1);

    /**
     * Verify we can load the in_progress screen
     */
    $submission = Submission::first();
    $this->assertModelExists($submission);
    $uuid = $submission->uuid;

    $response = $this->actingAs(self::$user)->get("/inprogress/${uuid}");
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->url("/inprogress/${uuid}")
    );

    /**
     * Answer the question:
     * Two fields, Product Name and Business Owner
     */
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Product Name"] = "Product ABC";
    $answers["answers"]["Business Owner"] = "bo@zaita.com";
    $answers["question"] = "Q1";
    
    $response = $this->actingAs(self::$user)->post("/inprogress/${uuid}", $answers);

    // Verify we've landed on the review screen
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.review", $uuid);

    // Submit the review page
    $response = $this->actingAs(self::$user)->post("/submit/${uuid}");
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    // View the page as the submitter to verify they can't approve
    $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'submitted')
      ->where('submission.nice_status', 'Ready to submit')
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    // Submit the submission
    $response = $this->actingAs(self::$user)->post("/submitforapproval/${uuid}");
    $response->assertRedirectToRoute("submission.submitted", $uuid);
    $response = $this->actingAs(self::$user)->get("/submitted/${uuid}");
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    // View the page as business owner to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->business)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    // View the page as ciso to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->ciso)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    // View the page as a security architect to verify assign_to_me functionality
    $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', true)
      ->where('can_approve_with_type', false)
    );

    // View the page as business owner to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->business)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    // Assign the submission to the security architect
    $response = $this->actingAs($this->security)->post("/assigntome/${uuid}");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', 'endorsement')
    );

    // View the page as business owner to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->business)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );    

    // Endorse the submission as security architect
    $response = $this->actingAs($this->security)->post("/approve/${uuid}");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    $response = $this->actingAs($this->security)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')          
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', false)
    );

    // View the page as ciso to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->ciso)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );    

    // View the page as business owner to ensure no assign_to_me or approve functionality
    $response = $this->actingAs($this->business)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', 'approval')
    ); 
    
    // Approve the Submission as business owner
    $response = $this->actingAs($this->business)->post("/approve/${uuid}");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    $response = $this->actingAs($this->business)->get("/submitted/${uuid}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/${uuid}")
      ->where('submission.status', 'approved')
      ->where('submission.nice_status', 'Approved')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );     
  }
}
