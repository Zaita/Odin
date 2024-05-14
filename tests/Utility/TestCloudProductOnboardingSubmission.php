<?php

namespace Tests\Utility;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;


use App\Models\User;
use App\Models\Submission;
use App\Models\Pillar;

class TestCloudProductOnboardingSubmission extends TestCase {

  public $user = null;
  public $securityArchitect = null;  
  public $ciso = null;
  public $businessOwner = null;

  public $pillarId = null;
  public $uuid = null;

  protected function setUp() : void {
    parent::setUp();
    $this->user = User::Factory()->create();
    $this->securityArchitect = User::where(['email' => 'security@zaita.com'])->first();
    $this->ciso = User::where(['email' => 'ciso@zaita.com'])->first();
    $this->businessOwner = User::where(['email' => 'bo@zaita.com'])->first();
    $this->pillarId = Pillar::where(["name" => "Cloud Product Onboarding"])->first()->id;
  }

  /**
   * Create the submission all the way until the submitted screen
   */
  protected function create_submission_with_no_tasks(): void {
    // Load the /start page
    $response = $this->actingAs($this->user)->get("/start/$this->pillarId");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $this->pillarId)
        ->where("pillar.name", "Cloud Product Onboarding")
    );

    // create the submission and verify redirection
    $response = $this->actingAs($this->user)->post("/start/$this->pillarId");
    $response->assertStatus(302);
    $response->assertRedirectContains("/inprogress/");
    $uuid = explode("inprogress/", $response->headers->get('Location'))[1];
    $this->uuid = $uuid;

    // Validate redirect matches what we load from database
    $submission = Submission::orderBy('id', 'desc')->first();
    $this->assertModelExists($submission);
    $dbUUID = $submission->uuid;
    $this->assertEquals($this->uuid, $dbUUID);

    // load the inprogress screen and first question
    $response = $this->actingAs($this->user)->get("/inprogress/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("submission")
      ->has("submission.answer_data")
      ->url("/inprogress/$this->uuid")
    );

    // Answer 1st question - Product Details
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Product Name"] = "Odin";
    $answers["answers"]["Product URL"] = "https://www.zaita.com";
    $answers["answers"]["Contact Email"] = "scott@odin.zaita.com";
    $answers["question"] = "Product Information";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);

    // validate 1st response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Odin")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Product Information");
        $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
        $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
        $this->assertEquals($json->answers[0]->data[1]->field, "Product URL");
        $this->assertEquals($json->answers[0]->data[1]->value, "https://www.zaita.com");
        $this->assertEquals($json->answers[0]->data[2]->field, "Contact Email");
        $this->assertEquals($json->answers[0]->data[2]->value, "scott@odin.zaita.com");                
        $this->assertEquals($json->answers[0]->status, "complete");
        $this->assertEquals($json->answers[0]->question, "Product Information");
        return true;
       })
      ->url("/inprogress/$this->uuid")
    );

    // Answer 2nd question - Business Owner
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Full Name"] = "Scott Rasmussen";
    $answers["answers"]["Email"] = "bo@zaita.com";
    $answers["question"] = "Business Owner";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);

    // validate 2nd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Odin")
      ->where("submission.business_owner", "bo@zaita.com")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Business Owner");
        // Q2 - Business Owner
        $this->assertEquals($json->answers[1]->data[0]->field, "Full Name");
        $this->assertEquals($json->answers[1]->data[0]->value, "Scott Rasmussen");
        $this->assertEquals($json->answers[1]->data[1]->field, "Email");
        $this->assertEquals($json->answers[1]->data[1]->value, "bo@zaita.com");        
        $this->assertEquals($json->answers[1]->status, "complete");
        $this->assertEquals($json->answers[1]->question, "Business Owner");        
        return true;
       })
      ->url("/inprogress/$this->uuid")
    );    

    // Answer 3rd Question
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "A sample description for our submission";
    $answers["question"] = "Product Description";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);

    // validate 3rd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Odin")
      ->where("submission.business_owner", "bo@zaita.com")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Product Description");
        // Q1 - Project Name
        $this->assertEquals($json->answers[2]->data[0]->field, "Description");
        $this->assertEquals($json->answers[2]->data[0]->value, "A sample description for our submission");
        $this->assertEquals($json->answers[2]->status, "complete");
        $this->assertEquals($json->answers[2]->question, "Product Description");
        return true;
        })
      ->url("/inprogress/$this->uuid")
    ); 

    // Answer 4th Question - Release Date
    $this->assertEquals(date_default_timezone_get(), "UTC");

    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Date"] = "1983-08-06T12:00:00.000Z";
    $answers["question"] = "Release Date";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.release_date.date", "1983-08-06 12:00:00.000000")
      ->where("submission.release_date.timezone", "Z")   
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Release Date");
        return true;
      })
    );

    // Answer 5th Question - Personal Information
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "No";
    $answers["question"] = "Personal Information";
    $response = $this->actingAs($this->user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[4]->data[0]->field, "action");
        $this->assertEquals($json->answers[4]->data[0]->value, "No");        
        $this->assertEquals($json->last_question, "Personal Information");
        return true;
      })
    );

    // Answer 6th Question - Types of Data
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Data"] = "A random list of data fields";
    $answers["question"] = "Types of Data";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[5]->data[0]->field, "Data");
        $this->assertEquals($json->answers[5]->data[0]->value, "A random list of data fields");             
        $this->assertEquals($json->last_question, "Types of Data");
        return true;
      })
    );

    // Answer 7th Question - Internal Integration
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "No";
    $answers["question"] = "Internal Integration";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[6]->data[0]->field, "action");
        $this->assertEquals($json->answers[6]->data[0]->value, "No"); 
        // Check that questions 8 and 9 were skipped
        $this->assertEquals($json->answers[7]->question, "Data Transfer");
        $this->assertEquals($json->answers[7]->status, "not_applicable");
        $this->assertEquals($json->answers[8]->question, "Description of Information");
        $this->assertEquals($json->answers[8]->status, "not_applicable");
        $this->assertEquals($json->last_question, "Firewall Changes");
        return true;
      })
    );

    // Answer 10th Question - Firewall Changes
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "No";
    $answers["question"] = "Firewall Changes";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers); 
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[9]->data[0]->field, "action");
        $this->assertEquals($json->answers[9]->data[0]->value, "No");      
        $this->assertEquals($json->last_question, "Firewall Changes");
        return true;
      })
    );

    // Answer 11th Question - Users
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Users"] = "Bob Bobinson, Sarah Lee";
    $answers["question"] = "Users";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[10]->data[0]->field, "Users");
        $this->assertEquals($json->answers[10]->data[0]->value, "Bob Bobinson, Sarah Lee");              
        $this->assertEquals($json->last_question, "Users");
        return true;
      })
    );

    // Answer 12th Question - Authentication
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "No";
    $answers["question"] = "Authentication";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[11]->data[0]->field, "action");
        $this->assertEquals($json->answers[11]->data[0]->value, "No");             
        $this->assertEquals($json->last_question, "Authentication");
        return true;
      })
    );

    // Answer 13th Question - Other
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Information"] = "n/a";
    $answers["question"] = "Other";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[12]->question, "Other");
        $this->assertEquals($json->answers[12]->data[0]->field, "Information");
        $this->assertEquals($json->answers[12]->data[0]->value, "n/a");           
        $this->assertEquals($json->last_question, "Other");
        return true;
      })
    );

    // Answer 14th Question - Next Steps
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "Review my answers";
    $answers["question"] = "Next Steps";
    $response = $this->actingAs($this->user)->post("/inprogress/$this->uuid", $answers);   

    // Verify we've landed on the review screen
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.review", $this->uuid);
    $response = $this->actingAs($this->user)->get("/review/$this->uuid");
    
    // Validate review
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/Review")
    ->has("ziggy.location")
    ->has("siteConfig")
    ->has("errors", 0)
    ->has("submission")
    ->where("submission.product_name", "Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "in_progress")
    ->has("submission.answer_data")      
    ->where("submission.answer_data", function (string $value) { 
      $json = json_decode($value);
      $this->assertEquals($json->last_question, "Next Steps");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Product Information");        
      return true;
    })
    ->where("submission.risk_data", "{}")
    ->url("/review/$uuid")
    ); 
    
    $response->assertSessionHasNoErrors();

  }

  public function submit_submission($expectedTasks = 0) {
    // Submit the review page
    $response = $this->actingAs($this->user)->post("/submit/$this->uuid");
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    // Check submitted screen
    $response = $this->actingAs($this->user)->get("/submitted/$this->uuid");

    // Validate submission has been completed
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/Submitted")
    ->has("ziggy.location")
    ->has("siteConfig")
    ->has("errors", 0)
    ->has("tasks", $expectedTasks)
    ->has("submission")
    ->where("submission.product_name", "Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "waiting_for_approval")
    ->has("submission.answer_data")      
    ->where("submission.answer_data", function (string $value) { 
      $json = json_decode($value);
      $this->assertEquals($json->last_question, "Next Steps");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Product Information");        
      return true;
    })
    ->where("submission.risk_data", "{}")
    ->url("/submitted/$this->uuid")
    );     
  }

  /**
   * View the submitted page for the submission to verify the permissions of the target user. We want
   * to verify if the target user can assign the submission to themselves or not.
   */
  public function view_submitted_screen($targetUser, $status, $niceStatus, $isAnApprover, $canBeAssigned, $canApproveWithType) {
    // View the page as the target user to verify assign_to_me functionality
    $response = $this->actingAs($targetUser)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', $status)
      ->where('submission.nice_status', $niceStatus)      
      ->where('is_an_approver', $isAnApprover)
      ->where('can_be_assigned', $canBeAssigned)
      ->where('can_approve_with_type', $canApproveWithType)
    );
  }

  /**
   * Assign the submission to the target user
   */
  public function assign_submission_to($targetUser, $approvalType) {
    $response = $this->actingAs($targetUser)->post("/assigntome/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($targetUser)->get("/submitted/$this->uuid");
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
      ->where('can_approve_with_type', $approvalType)
    );
  }

  /**
   * Approve the submission as the target user
   */
  public function approve_submission($targetUser, $expectedStatus, $expectedNiceStatus) {
    $response = $this->actingAs($targetUser)->post("/approve/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($targetUser)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', $expectedStatus)
      ->where('submission.nice_status', $expectedNiceStatus)      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', false)
    );
  }

  /**
   * Deny the submission as the target user
   */
  public function deny_submission($targetUser, $expectedStatus, $expectedNiceStatus) {
    $response = $this->actingAs($targetUser)->post("/deny/$this->uuid");
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.submitted", $this->uuid);

    $response = $this->actingAs($targetUser)->get("/submitted/$this->uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$this->uuid")
      ->where('submission.status', $expectedStatus)
      ->where('submission.nice_status', $expectedNiceStatus)      
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false) // Already assigned
      ->where('can_approve_with_type', false)
    );
  }
}
