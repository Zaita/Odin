<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;


use App\Models\User;
use App\Models\Submission;
use App\Models\Pillar;

class ProofOfConceptTest extends TestCase {

  private static $user = null;
  private $id = null;
  private $uuid = null;

  protected function setUp() : void {
    parent::setUp();
    self::$user = User::where(['email' => 'admin@zaita.com'])->first();
    $this->id = Pillar::where(["name" => "Proof of Concept"])->first()->id;
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
        ->where("pillar.name", "Proof of Concept")
    );
  }

  /**
   * CHeck that we can create a new submission
   */
  public function test_do_submission(): void {    
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
        ->where("pillar.name", "Proof of Concept")
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
    $answers["question"] = "Product Details";
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
        $this->assertEquals($json->last_question, "Product Details");
        $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
        $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
        $this->assertEquals($json->answers[0]->data[1]->field, "Product URL");
        $this->assertEquals($json->answers[0]->data[1]->value, "https://www.zaita.com");
        $this->assertEquals($json->answers[0]->data[2]->field, "Contact Email");
        $this->assertEquals($json->answers[0]->data[2]->value, "scott@odin.zaita.com");                
        $this->assertEquals($json->answers[0]->status, "complete");
        $this->assertEquals($json->answers[0]->question, "Product Details");
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
    $answers["answers"]["action"] = "Proof of Concept";
    $answers["question"] = "Proof of Concept or Trial";
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
        $this->assertEquals($json->last_question, "Proof of Concept or Trial");
        // Q1 - Project Name
        $this->assertEquals($json->answers[2]->data[0]->field, "action");
        $this->assertEquals($json->answers[2]->data[0]->value, "Proof of Concept");
        $this->assertEquals($json->answers[2]->status, "complete");
        $this->assertEquals($json->answers[2]->question, "Proof of Concept or Trial");
        return true;
        })
      ->url("/inprogress/$uuid")
    ); 

    // Answer 4th Question - Product Description
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "Yes";
    $answers["question"] = "Product Description";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Product Description");
        return true;
      })
    );

    // Answer 5th Question - SaaS or On-Premises
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["action"] = "Software as a Service";
    $answers["question"] = "SaaS or On-Premises";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "SaaS or On-Premises");
        return true;
      })
    );

    // Answer 6th Question - Key Stakeholders
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "Bob Bobinson";
    $answers["question"] = "Key Stakeholders";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Key Stakeholders");
        return true;
      })
    );

    // Answer 7th Question - Firewall Changes
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
        $this->assertEquals($json->last_question, "Firewall Changes");
        return true;
      })
    );

    // Answer 8th Question - Firewall Information
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "A";
    $answers["question"] = "Firewall Information";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Firewall Information");
        return true;
      })
    );

    // Answer 9th Question - Description of Information
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "0123456789";
    $answers["question"] = "Description of Information";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Description of Information");
        return true;
      })
    );

    // Answer 10th Question - Data Safety
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "01234567890123456789";
    $answers["question"] = "Data Safety";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Data Safety");
        return true;
      })
    );

    // Answer 11th Question - Experiment Users
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "A";
    $answers["question"] = "Experiment Users";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Experiment Users");
        return true;
      })
    );

    // Answer 12th Question - Expected Outcomes
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "01234567890123456789";
    $answers["question"] = "Expected Outcomes";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Expected Outcomes");
        return true;
      })
    );

    // Answer 13th Question - Evaluation Method
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Description"] = "01234567890123456789";
    $answers["question"] = "Evaluation Method";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Evaluation Method");
        return true;
      })
    );

    // Answer 14th Question - Time-Span
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Time-Span"] = "2 Weeks";
    $answers["question"] = "Time-Span";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Time-Span");
        return true;
      })
    );

    // Answer 15th Question - Start Date
    $this->assertEquals(date_default_timezone_get(), "UTC");

    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Start Date"] = "1983-08-06T12:00:00.000Z";
    $answers["question"] = "Start Date";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);   
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("errors", 0)
      ->where("submission.release_date.date", "1983-08-06 12:00:00.000000")
      ->where("submission.release_date.timezone", "Z")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Start Date");
        return true;
      })
    );

    // Answer 16th Question - Other
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Information"] = "A";
    $answers["question"] = "Other";
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
      $this->assertEquals($json->last_question, "Other");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Product Details");        
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
    ->has("submission")
    ->where("submission.product_name", "Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "approved")
    ->has("submission.answer_data")      
    ->where("submission.answer_data", function (string $value) { 
      $json = json_decode($value);
      $this->assertEquals($json->last_question, "Other");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Product Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Product Details");        
      return true;
    })
    ->where("submission.risk_data", "{}")
    ->url("/submitted/$uuid")
    ); 
  }  
}
