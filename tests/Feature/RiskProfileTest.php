<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

use App\Models\User;
use App\Models\Submission;
use App\Models\Pillar;

class RiskProfileTest extends TestCase {

  private static $user = null;
  private $id = null;

  protected function setUp() : void {
    parent::setUp();
    self::$user = User::Factory()->create();
    $this->id = Pillar::where(["name" => "Risk Profile"])->first()->id;
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
        ->where("pillar.name", "Risk Profile")
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
        ->where("pillar.name", "Risk Profile")
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

    // Answer 1st question - Project Name
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Name"] = "Project Odin";
    $answers["question"] = "Project Name";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

    // validate 1st response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Project Odin")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Project Name");
        $this->assertEquals($json->answers[0]->data[0]->field, "Name");
        $this->assertEquals($json->answers[0]->data[0]->value, "Project Odin");
        $this->assertEquals($json->answers[0]->status, "complete");
        $this->assertEquals($json->answers[0]->question, "Project Name");
        return true;
       })
      ->url("/inprogress/$uuid")
    );

    // Answer 2nd question - Go-Live Date
    $this->assertEquals(date_default_timezone_get(), "UTC");

    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Date"] = "1983-08-06T12:00:00.000Z";
    $answers["question"] = "Go-Live Date";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

    // validate 2nd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Project Odin")
      ->where("submission.release_date.date", "1983-08-06 12:00:00.000000")
      ->where("submission.release_date.timezone", "Z")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Go-Live Date");
        // Q1 - Project Name
        $this->assertEquals($json->answers[0]->data[0]->field, "Name");
        $this->assertEquals($json->answers[0]->data[0]->value, "Project Odin");
        $this->assertEquals($json->answers[0]->status, "complete");
        $this->assertEquals($json->answers[0]->question, "Project Name");
        // Q2 - Go Live Date
        $this->assertEquals($json->answers[1]->data[0]->field, "Date");
        $this->assertEquals($json->answers[1]->data[0]->value, "1983-08-06T12:00:00.000Z");
        $this->assertEquals($json->answers[1]->status, "complete");
        $this->assertEquals($json->answers[1]->question, "Go-Live Date");        
        return true;
       })
      ->url("/inprogress/$uuid")
    );    

    // Answer 3rd Question
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Answer"] = "Yes";
    $answers["question"] = "Compliance Requirement";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);

    // validate 3rd response
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->has("submission")
      ->where("submission.product_name", "Project Odin")
      ->where("submission.release_date", "1983-08-06 12:00:00")
      ->where("submission.status", "in_progress")
      ->has("submission.answer_data")      
      ->where("submission.answer_data", function (string $value) { 
        $json = json_decode($value);
        $this->assertEquals($json->last_question, "Compliance Requirement");
        // Q1 - Project Name
        $this->assertEquals($json->answers[0]->data[0]->field, "Name");
        $this->assertEquals($json->answers[0]->data[0]->value, "Project Odin");
        $this->assertEquals($json->answers[0]->status, "complete");
        $this->assertEquals($json->answers[0]->question, "Project Name");
        // Q2 - Go Live Date
        $this->assertEquals($json->answers[1]->data[0]->field, "Date");
        $this->assertEquals($json->answers[1]->data[0]->value, "1983-08-06T12:00:00.000Z");
        $this->assertEquals($json->answers[1]->status, "complete");
        $this->assertEquals($json->answers[1]->question, "Go-Live Date");     
        // Q3 - Compliance Requirement
        $this->assertEquals($json->answers[2]->data[0]->field, "Answer");
        $this->assertEquals($json->answers[2]->data[0]->value, "Yes");
        $this->assertEquals($json->answers[2]->status, "complete");
        $this->assertEquals($json->answers[2]->question, "Compliance Requirement");           
        return true;
        })
      ->url("/inprogress/$uuid")
    ); 

    // Answer 4th Question
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Answer"] = "Yes";
    $answers["question"] = "Business Continuity";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/InProgress")
    ->has("errors", 0)
    );

    // Answer 5th Question
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Answer"] = "Yes";
    $answers["question"] = "Customer Impact";
    $response = $this->actingAs(self::$user)->post("/inprogress/$uuid", $answers);    
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/InProgress")
    ->has("errors", 0)
    );

    // Answer 6th Question (last question)
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Answer"] = "Yes";
    $answers["question"] = "Clear Requirements";
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
    ->where("submission.product_name", "Project Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "in_progress")
    ->has("submission.answer_data")      
    ->where("submission.answer_data", function (string $value) { 
      $json = json_decode($value);
      $this->assertEquals($json->last_question, "Clear Requirements");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Project Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Project Name");
      // Q2 - Go Live Date
      $this->assertEquals($json->answers[1]->data[0]->field, "Date");
      $this->assertEquals($json->answers[1]->data[0]->value, "1983-08-06T12:00:00.000Z");
      $this->assertEquals($json->answers[1]->status, "complete");
      $this->assertEquals($json->answers[1]->question, "Go-Live Date");     
      // Q3 - Compliance Requirement
      $this->assertEquals($json->answers[2]->data[0]->field, "Answer");
      $this->assertEquals($json->answers[2]->data[0]->value, "Yes");
      $this->assertEquals($json->answers[2]->status, "complete");
      $this->assertEquals($json->answers[2]->question, "Compliance Requirement");           
      return true;
    })
    ->where("submission.risk_data", function(string $value) {
      $json = json_decode($value);
      // Risk 1
      $this->assertEquals($json[0]->name, "Compliance failure");
      $this->assertEquals($json[0]->score, "100");
      $this->assertEquals($json[0]->rating, "Severe");
      // Risk 2
      $this->assertEquals($json[1]->name, "Business continuity");
      $this->assertEquals($json[1]->score, "60");
      $this->assertEquals($json[1]->rating, "Moderate");    
      // Risk 3
      $this->assertEquals($json[2]->name, "Cost overrun");
      $this->assertEquals($json[2]->score, "65");
      $this->assertEquals($json[2]->rating, "Moderate");  
      // Risk 3
      $this->assertEquals($json[3]->name, "User dissatisfaction");
      $this->assertEquals($json[3]->score, "60");
      $this->assertEquals($json[3]->rating, "Moderate");       
      // Risk 5
      $this->assertEquals($json[4]->name, "Functional failure");
      $this->assertEquals($json[4]->score, "70");
      $this->assertEquals($json[4]->rating, "Severe");                  

      return true;
    })
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
    ->where("submission.product_name", "Project Odin")
    ->where("submission.release_date", "1983-08-06 12:00:00")
    ->where("submission.status", "approved")
    ->has("submission.answer_data")      
    ->where("submission.answer_data", function (string $value) { 
      $json = json_decode($value);
      $this->assertEquals($json->last_question, "Clear Requirements");
      // Q1 - Project Name
      $this->assertEquals($json->answers[0]->data[0]->field, "Name");
      $this->assertEquals($json->answers[0]->data[0]->value, "Project Odin");
      $this->assertEquals($json->answers[0]->status, "complete");
      $this->assertEquals($json->answers[0]->question, "Project Name");
      // Q2 - Go Live Date
      $this->assertEquals($json->answers[1]->data[0]->field, "Date");
      $this->assertEquals($json->answers[1]->data[0]->value, "1983-08-06T12:00:00.000Z");
      $this->assertEquals($json->answers[1]->status, "complete");
      $this->assertEquals($json->answers[1]->question, "Go-Live Date");     
      // Q3 - Compliance Requirement
      $this->assertEquals($json->answers[2]->data[0]->field, "Answer");
      $this->assertEquals($json->answers[2]->data[0]->value, "Yes");
      $this->assertEquals($json->answers[2]->status, "complete");
      $this->assertEquals($json->answers[2]->question, "Compliance Requirement");           
      return true;
    })
    ->where("submission.risk_data", function(string $value) {
      $json = json_decode($value);
      // Risk 1
      $this->assertEquals($json[0]->name, "Compliance failure");
      $this->assertEquals($json[0]->score, "100");
      $this->assertEquals($json[0]->rating, "Severe");
      // Risk 2
      $this->assertEquals($json[1]->name, "Business continuity");
      $this->assertEquals($json[1]->score, "60");
      $this->assertEquals($json[1]->rating, "Moderate");    
      // Risk 3
      $this->assertEquals($json[2]->name, "Cost overrun");
      $this->assertEquals($json[2]->score, "65");
      $this->assertEquals($json[2]->rating, "Moderate");  
      // Risk 3
      $this->assertEquals($json[3]->name, "User dissatisfaction");
      $this->assertEquals($json[3]->score, "60");
      $this->assertEquals($json[3]->rating, "Moderate");       
      // Risk 5
      $this->assertEquals($json[4]->name, "Functional failure");
      $this->assertEquals($json[4]->score, "70");
      $this->assertEquals($json[4]->rating, "Severe");                  

      return true;
    })
    ->url("/submitted/$uuid")
    ); 
  }  
}
