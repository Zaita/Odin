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
use App\Models\TaskSubmission;
use Tests\Utility\SubmissionCreator;

/**
 * Test creating a submission that has a simple task, then
 * allowing a collaborator to complete the task
 */
class SubmissionTaskWithCollaboratorTest extends TestCase {
  // Load our Seeds
  // use RefreshDatabase;
  // use SubmissionCreator;

  /**
   * Test creating a submission and completing the created task.
   * This task specifically won't have an information screen
   * Then approve it as the Security Architect
   */
  public function test_submission_with_collaborator() : void {
    // $submission = $this->createSubmission("Test Single Task Submission", "user@zaita.com", true);
    // $uuid = $submission->uuid;
    // /**
    //  * Get the UUID of the task
    //  */
    // $task = TaskSubmission::where(['submission_id' => $submission->id])->first();
    // $this->assertTrue($task != null);
    // $tuuid = $task->uuid;
    // $task->show_information_screen = false;
    // $task->save();

    // // Trying to start a task as a not-specified collaborator should error
    // $response = $this->actingAs($this->collab)->get("/task/$tuuid");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // $response = $this->actingAs($this->collab)->get("/submitted/$uuid");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 1)
    //   ->where('errors.error', 'You do not have access to complete tasks on this submission')
    //   ->url("/submitted/$uuid")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Tasks to complete')
    //   ->has('tasks', 1)
    //   ->where('tasks.0.name', 'One Question Task')
    //   ->where('tasks.0.status', 'ready_to_start')
    //   ->has('collaborators', 0)
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Add the collaborator
    // $data = array();
    // $data["email"] = "collab@zaita.com";
    // $response = $this->actingAs($this->user)->post("/submission/$uuid/collaborators/add", $data);
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.submitted", $uuid);

    // // Check we have the collaborator name returned in the submitted page now
    // $response = $this->actingAs($this->user)->get("/submitted/$uuid");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)      
    //   ->url("/submitted/$uuid")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Tasks to complete')
    //   ->has('tasks', 1)
    //   ->where('tasks.0.name', 'One Question Task')
    //   ->where('tasks.0.status', 'ready_to_start')
    //   ->has('collaborators', 1)
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );

    // // Start the task on the submission as the collaborator now and it should work
    // $response = $this->actingAs($this->collab)->get("/task/$tuuid");
    // $response->assertStatus(302);
    // $response->assertRedirectToRoute("submission.task.inprogress", $tuuid);

    // $response = $this->actingAs($this->collab)->get("/submitted/$uuid");
    // $response->assertInertia(fn (Assert $page) => $page
    //   ->component("Submission/Submitted")
    //   ->has("ziggy.location")
    //   ->has("siteConfig")
    //   ->has("errors", 0)
    //   ->url("/submitted/$uuid")
    //   ->where('submission.status', 'submitted')
    //   ->where('submission.nice_status', 'Tasks to complete')
    //   ->has('tasks', 1)
    //   ->where('tasks.0.name', 'One Question Task')
    //   ->where('tasks.0.status', 'in_progress')
    //   ->has('collaborators', 1)
    //   ->where('is_an_approver', false)
    //   ->where('can_be_assigned', false)
    //   ->where('can_approve_with_type', false)
    // );
  }
}
