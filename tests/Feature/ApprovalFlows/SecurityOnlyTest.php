<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;

use App\Models\User;
use App\Models\Submission;
use App\Models\Pillar;
use Tests\Utility\TestProofOfConceptSubmission;
// use Tests\Feature\Pillars\ProofOfConceptTest;

class SecurityOnlyTest extends TestProofOfConceptSubmission {
  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * Check that we can load the /start page for our Proof of Concept Pillar
   */
  public function test_good_approval(): void {
    $this->create_submission();
    $this->submit_submission();    

    // View the page as a security architect to verify assign_to_me functionality
    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', true)
      ->where('can_approve_with_type', false)
    );

    // Assign the submission to the security architect
    $response = $this->actingAs($this->securityArchitect)->post("/assigntome/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', 'approval')
    );
  
    // Approve the submission
    $response = $this->actingAs($this->securityArchitect)->post("/approve/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'approved')
      ->where('submission.nice_status', 'Approved')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', false)
    );
  }

  /**
   * Check that we can load the /start page for our Proof of Concept Pillar
   */
  public function test_deny_approval(): void {
    $this->create_submission();
    $this->submit_submission();    

    // View the page as a security architect to verify assign_to_me functionality
    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', true)
      ->where('can_approve_with_type', false)
    );

    // Assign the submission to the security architect
    $response = $this->actingAs($this->securityArchitect)->post("/assigntome/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'waiting_for_approval')
      ->where('submission.nice_status', 'Waiting for approval')      
      ->where('is_an_approver', true)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', 'approval')
    );
  
    // Approve the submission
    $response = $this->actingAs($this->securityArchitect)->post("/deny/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($this->securityArchitect)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', 'denied')
      ->where('submission.nice_status', 'Not approved')      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', false)
    );
  }
}
