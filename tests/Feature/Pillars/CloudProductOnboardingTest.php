<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;


use App\Models\User;
use App\Models\Submission;
use App\Models\Pillar;

class CloudProductOnboardingTest extends TestCase {

  private static $user = null;
  private $id = null;
  private $uuid = null;

  protected function setUp() : void {
    parent::setUp();
    self::$user = User::where(['email' => 'admin@zaita.io'])->first();
    $this->id = Pillar::where(["name" => "Cloud Product Onboarding"])->first()->id;
  }

  /**
   * Check that we can load the /start page for our Proof of Concept Pillar
   */
  public function test_start_page(): void {
    $id = $this->id;
    $response = $this->actingAs(self::$user)->get("/start/$id");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $id)
        ->where("pillar.name", "Cloud Product Onboarding")
    );
  }

  /**
   * Create a Cloud Product Onboarding submission with all tasks
   */
  public function test_do_submission_with_all_tasks(): void {    
    $id = $this->id;    

    // Load the /start page
    $response = $this->actingAs(self::$user)->get("/start/$id");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $id)
        ->where("pillar.name", "Cloud Product Onboarding")
    );

    // create the submission and verify redirection
    $response = $this->actingAs(self::$user)->post("/start/$id");
    $response->assertStatus(302);
    $response->assertRedirectContains("/inprogress/");
    $uuid = explode("inprogress/", $response->headers->get('Location'))[1];

    // Validate redirect matches what we load from database
    $submission = Submission::orderBy('id', 'desc')->first();
    $this->assertModelExists($submission);
    $dbUUID = $submission->uuid;
    $this->assertEquals($uuid, $dbUUID);

    // load the inprogress screen and first question
    $response = $this->actingAs(self::$user)->get("/inprogress/$uuid");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("submission")
      ->has("submission.answer_data")
      ->url("/inprogress/$uuid")
    );

    // Answer 1st question - Product Details
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Product Name"] = "Odin";
    $answers["answers"]["Product URL"] = "https://www.zaita.com";
    $answers["answers"]["Contact Email"] = "scott@odin.zaita.com";
    $answers["question"] = "Product Information";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

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
      ->url("/inprogress/$uuid")
    );

    // Answer 2nd question - Business Owner
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Full Name"] = "Scott Rasmussen";
    $answers["answers"]["Email"] = "scott.bo@zaita.com";
    $answers["question"] = "Business Owner";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

    // validate 2nd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Odin")
      ->where("submission.business_owner", "scott.bo@zaita.com")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Business Owner");
        // Q2 - Business Owner
        $this->assertEquals($json->answers[1]->data[0]->field, "Full Name");
        $this->assertEquals($json->answers[1]->data[0]->value, "Scott Rasmussen");
        $this->assertEquals($json->answers[1]->data[1]->field, "Email");
        $this->assertEquals($json->answers[1]->data[1]->value, "scott.bo@zaita.com");        
        $this->assertEquals($json->answers[1]->status, "complete");
        $this->assertEquals($json->answers[1]->question, "Business Owner");        
        return true;
       })
      ->url("/inprogress/$uuid")
    );    

    // Answer 3rd Question
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "A sample description for our submission";
    $answers["question"] = "Product Description";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

    // validate 3rd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Odin")
      ->where("submission.business_owner", "scott.bo@zaita.com")
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
      ->url("/inprogress/$uuid")
    ); 

    // Answer 4th Question - Release Date
    $this->assertEquals(date_default_timezone_get(), "UTC");

    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Date"] = "1983-08-06T12:00:00.000Z";
    $answers["question"] = "Release Date";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
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
    $answers["answers"]["action"] = "Yes";
    $answers["question"] = "Personal Information";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[4]->data[0]->field, "action");
        $this->assertEquals($json->answers[4]->data[0]->value, "Yes");        
        $this->assertEquals($json->last_question, "Personal Information");
        return true;
      })
    );

    // Answer 6th Question - Types of Data
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Data"] = "A random list of data fields";
    $answers["question"] = "Types of Data";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
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
    $answers["answers"]["action"] = "Yes";
    $answers["question"] = "Internal Integration";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[6]->data[0]->field, "action");
        $this->assertEquals($json->answers[6]->data[0]->value, "Yes");          
        $this->assertEquals($json->last_question, "Internal Integration");
        return true;
      })
    );

    // Answer 8th Question - Data Transfer
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "Describing the data transfer";
    $answers["question"] = "Data Transfer";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[7]->data[0]->field, "Description");
        $this->assertEquals($json->answers[7]->data[0]->value, "Describing the data transfer");   
        $this->assertEquals($json->last_question, "Data Transfer");
        return true;
      })
    );

    // Answer 9th Question - Description of Information
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "01234567890123456789";
    $answers["question"] = "Description of Information";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[8]->data[0]->field, "Description");
        $this->assertEquals($json->answers[8]->data[0]->value, "01234567890123456789");           
        $this->assertEquals($json->last_question, "Description of Information");
        return true;
      })
    );

    // Answer 10th Question - Firewall Changes
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "Yes";
    $answers["question"] = "Firewall Changes";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers); 
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[9]->data[0]->field, "action");
        $this->assertEquals($json->answers[9]->data[0]->value, "Yes");      
        $this->assertEquals($json->last_question, "Firewall Changes");
        return true;
      })
    );

    // Answer 11th Question - Users
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Users"] = "Bob Bobinson, Sarah Lee";
    $answers["question"] = "Users";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
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
    $answers["answers"]["action"] = "Yes";
    $answers["question"] = "Authentication";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->answers[11]->data[0]->field, "action");
        $this->assertEquals($json->answers[11]->data[0]->value, "Yes");             
        $this->assertEquals($json->last_question, "Authentication");
        return true;
      })
    );

    // Answer 13th Question - Other
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Information"] = "n/a";
    $answers["question"] = "Other";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
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
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   

    // Verify we've landed on the review screen
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.review", $uuid);
    $response = $this->actingAs(self::$user)->get("/review/$uuid");
    
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

    // Submit the review page
    $response = $this->actingAs(self::$user)->post("/submit/$uuid");
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    // Check submitted screen
    $response = $this->actingAs(self::$user)->get("/submitted/$uuid");

    // Validate submission has been completed
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/Submitted")
    ->has("ziggy.location")
    ->has("siteConfig")
    ->has("errors", 0)
    ->has("tasks", 4)
    ->where("tasks.0.name", "Privacy Threshold Assessment")
    ->where("tasks.1.name", "Third Party Assessment")
    ->where("tasks.2.name", "Security Risk Assessment Link")
    ->where("tasks.3.name", "Information and Data Management Assessment")
    ->has("submission")
    ->where("submission.product_name", "Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "submitted")
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
    ->url("/submitted/$uuid")
    ); 
  }  
}
