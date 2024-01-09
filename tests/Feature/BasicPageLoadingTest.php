<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Group;

class BasicPageLoadingTest extends TestCase {
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
  public function test_admin_home_page(): void {
    $response = $this->get('/admin');
    $response->assertRedirect($uri = "/login");
  }

  /**
   * Check that a non-admin user is banned from accessing the admin page
   */
  public function test_admin_home_page_without_admin_user(): void {
    $user = User::Factory()->create();

    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(403);
  }

  /**
   * Check that an admin user is allowed to access the admin page
   */
  public function test_admin_home_page_with_admin_user(): void {
    $user = User::Factory()->create();
    $group = Group::firstOrCreate(['name' => 'Administrator']);
    $group->addUser($user->id);

    $response = $this->actingAs($user)->get('/admin');
    $response->assertStatus(200);
    }
}
