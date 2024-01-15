<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;


use App\Models\User;
use App\Models\Group;
use App\Models\Submission;
use App\Models\Pillar;
use App\Models\Questionnaire;
use Database\Seeders\TestPillarSeeder;

class UserSubmissionInputTest extends TestCase {
  // Load our Seeds
  use RefreshDatabase;

  private static $user = null;
  private $id = null;
  private $uuid = null;

  protected function setUp() : void {
    parent::setUp();

    self::$user = User::Factory()->create();

    // Load Our Test Pillar for these tests
    $this->assertDatabaseCount('pillars', 4);
    $this->id = Pillar::where(["name" => "Test All Input Types"])->first()->id;
  }

  /**
   * Create a submission for us to work with
   */
  protected function createSubmission() : void {
    $id = $this->id;
    $response = $this->actingAs(self::$user)->get("/start/${id}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $id)
        ->where("pillar.name", "Test All Input Types")
    );

    $response = $this->actingAs(self::$user)->post("/start/${id}");
    $response->assertStatus(302);
    $this->assertDatabaseCount('submissions', 1);
  }

  /**
   * Check that we can load the /start page for our Proof of Concept Pillar
   */
  public function test_start_page(): void {
    $id = $this->id;
    $response = $this->actingAs(self::$user)->get("/start/${id}");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $id)
        ->where("pillar.name", "Test All Input Types")
    );
  }

  /**
   * CHeck that we can create a new submission
   */
  public function test_create_new_submission(): void {
    $this->createSubmission();
  }  

  /**
   * Check that we can load the inprogress screen for our new submission
   */
  public function test_load_inprogress_screen() : void {
    $this->createSubmission();

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
  }

  public function test_answer_questions() : void {
    $this->createSubmission();

    $submission = Submission::first();
    $this->assertModelExists($submission);
    $uuid = $submission->uuid;

    $response = $this->actingAs(self::$user)->get("/inprogress/${uuid}");
    $response->assertStatus(200);

    /**
     * Question One - All fields are optional
     */
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Email Field"] = "scott@zaita.com";
    $answers["question"] = "Question One";

    $response = $this->actingAs(self::$user)->post("/inprogress/${uuid}", $answers);
    
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/InProgress")
    ->has("ziggy.location")
    ->has("siteConfig")
    ->has("errors", 0)
    ->url("/inprogress/${uuid}")
    );

    /**
     * Question Two - All fields are required
     */
    $answers = array();
    $answers["answers"] = array();
    $answers["answers"]["Text Field"] = "Some Random Text";
    $answers["answers"]["Email Field"] = "scott@zaita.com";
    $answers["answers"]["Text Area"] = "Some content";
    $answers["answers"]["Rich Text Editor"] = "Some content";
    $answers["answers"]["Date Field"] = "01/12/2024";
    $answers["answers"]["URL Field"] = "https://www.zaita.com";
    $answers["question"] = "Question Two";

    $response = $this->actingAs(self::$user)->post("/inprogress/${uuid}", $answers);
    $response->assertInertia(fn (Assert $page) => $page
    ->component("Submission/InProgress")
    ->has("ziggy.location")
    ->has("siteConfig")
    ->has("errors", 0)
    ->url("/inprogress/${uuid}")
    );    

    $this->assertDatabaseCount('submissions', 1);
  }
}
