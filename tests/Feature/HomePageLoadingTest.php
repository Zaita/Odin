<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Group;

class HomePageLoadingTest extends TestCase {
  // Load our Seeds
  use RefreshDatabase;

  /**
   * A basic feature test example.
   */
  public function test_loading_home_page(): void {
    $response = $this->get('/');
    $response->assertRedirect($uri = "/login");
  }

  /**
   * Test loading the home page with an authenticated user
   */
  public function test_loading_home_page_authenticated(): void {
    $user = User::Factory()->create();

    $response = $this->actingAs($user)->get('/');
    $response->assertStatus(200);
  }

  /**
   * Check that the admin page requires a login
   */
  public function test_home_page_pillar_count(): void {
    $this->assertDatabaseCount('pillars', 5);

    $user = User::Factory()->create();

    $response = $this->actingAs($user)->get('/');
    $response->assertSuccessful();

    // $pillars = array("Risk Profile", "Proof of Concept", "Cloud Product Onboarding", "New Project or Product", "Product Release");
    // $response->assertSeeInOrder($pillars, $escaped = true);
    $response->assertSessionHasNoErrors();
  }
}
