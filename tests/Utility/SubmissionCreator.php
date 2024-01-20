<?php

namespace Tests\Utility;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

use App\Models\Submission;
use App\Models\User;
use App\Models\Pillar;

trait SubmissionCreator
{
  public ?User $user = null;
  public ?User $security = null;
  public ?User $collab = null;
  public ?Submission $submission = null;

  /**
   * Creates the application.
   */
  public function createSubmission($pillarName, $submitterEmail, $expectTasks): Submission
  {
    $this->user = User::where('email', '=', 'user@zaita.com')->first();
    $this->security = User::where('email', '=','sec@zaita.com')->first();
    $this->collab = User::where('email', '=','collab@zaita.com')->first();

    $this->assertTrue($this->user->email == "user@zaita.com");
    $this->assertTrue($this->security->email == "sec@zaita.com");
    $this->assertTrue($this->collab->email == "collab@zaita.com");

    $pillarId = Pillar::where(["name" => $pillarName])->first()->id;
    $response = $this->actingAs($this->user)->get("/start/$pillarId");
    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component("Start")
        ->has("ziggy.location")
        ->has("siteConfig")
        ->has("pillar")
        ->where("pillar.id", $pillarId)
        ->where("pillar.name", $pillarName)
    );

    $response = $this->actingAs($this->user)->post("/start/$pillarId");
    $response->assertStatus(302);

    /**
     * Verify we can load the in_progress screen
     */
    $submission = Submission::first();
    $this->assertModelExists($submission);
    $uuid = $submission->uuid;

    $response = $this->actingAs($this->user)->get("/inprogress/$uuid");
    $response->assertStatus(200);

    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/InProgress")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->url("/inprogress/$uuid")
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
    
    $response = $this->actingAs($this->user)->post("/inprogress/$uuid", $answers);

    // Verify we've landed on the review screen
    $response->assertStatus(302);
    $response->assertRedirectToRoute("submission.review", $uuid);

    // Submit the review page
    $response = $this->actingAs($this->user)->post("/submit/$uuid");
    $response->assertRedirectToRoute("submission.submitted", $uuid);

    // View the page as the submitter to verify there is a task available
    $response = $this->actingAs($this->user)->get("/submitted/$uuid");
    $response->assertInertia(fn (Assert $page) => $page
      ->component("Submission/Submitted")
      ->has("ziggy.location")
      ->has("siteConfig")
      ->has("errors", 0)
      ->url("/submitted/$uuid")
      ->where('submission.status', 'submitted')
      ->where('submission.nice_status', 'Tasks to complete')
      ->has('tasks', 1)
      ->where('tasks.0.name', 'One Question Task')
      ->where('tasks.0.status', 'ready_to_start')
      ->where('is_an_approver', false)
      ->where('can_be_assigned', false)
      ->where('can_approve_with_type', false)
    );

    $this->submission = $submission;
    return $submission;
  }
}
