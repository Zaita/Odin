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

class UserSubmissionTest extends TestCase {
  // Load our Seeds
  use RefreshDatabase;

  private static $user = null;
  protected function setUp() : void {
    parent::setUp();

    self::$user = User::Factory()->create();
  }

  /**
   * Check that we can load the /start page for our Proof of Concept Pillar
   */
  public function test_loading_start_page(): void {
    $response = $this->actingAs(self::$user)->get('/start/2');
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", 2)
        ->where("pillar.name", "Test Two Step Approval Flow")
    );

    $response = $this->actingAs(self::$user)->get('/start/1');
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", 1)
        ->where("pillar.name", "Test Single-Step Approval Flow")
    );
  }

  /**
   * CHeck that we can create a new submission
   */
  public function test_create_new_submission(): void {
    $this->assertDatabaseCount('submissions', 0);
    $response = $this->actingAs(self::$user)->post('/start/2');
    $this->assertDatabaseCount('submissions', 1);

    // We redirect after successful creation of a new submission
    $response->assertStatus(302);
  }  

  /**
   * Check that we can load the inprogress screen for our new submission
   */
  public function test_load_inprogress_screen() : void {
    $this->assertDatabaseCount('submissions', 0);
    $response = $this->actingAs(self::$user)->post('/start/2');
    $this->assertDatabaseCount('submissions', 1);

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
}
