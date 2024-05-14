<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\Utility\TestCloudProductOnboardingSubmission;

class TwoStageApprovalWithCISOEndorsementTest extends TestCloudProductOnboardingSubmission {

  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * Check that we can go through a happy scenario
   */
  public function test_good_approval(): void {
    $this->create_submission_with_no_tasks();
    $this->submit_submission();    

    $this->view_submitted_screen($this->securityArchitect, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->securityArchitect, "endorsement");
    $this->approve_submission($this->securityArchitect, "waiting_for_approval", "Waiting for approval");
  
    $this->view_submitted_screen($this->ciso, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->ciso, "endorsement");
    $this->approve_submission($this->ciso, "waiting_for_approval", "Waiting for approval");

    $this->view_submitted_screen($this->businessOwner, "waiting_for_approval", "Waiting for approval", true, false, "approval");
    $this->approve_submission($this->businessOwner, "approved", "Approved");
  } 

  /**
   * Check that approval continues, when security architect does not endorse the change.
   */
  public function test_no_security_architect_endorsement(): void {
    $this->create_submission_with_no_tasks();
    $this->submit_submission();    

    $this->view_submitted_screen($this->securityArchitect, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->securityArchitect, "endorsement");
    $this->deny_submission($this->securityArchitect, "waiting_for_approval", "Waiting for approval");
  
    $this->view_submitted_screen($this->ciso, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->ciso, "endorsement");
    $this->approve_submission($this->ciso, "waiting_for_approval", "Waiting for approval");

    $this->view_submitted_screen($this->businessOwner, "waiting_for_approval", "Waiting for approval", true, false, "approval");
    $this->approve_submission($this->businessOwner, "approved", "Approved");
  } 

  /**
   * Check that approval continues, when security architect and ciso do not endorse
   */
   public function test_no_ciso_endorsement(): void {
    $this->create_submission_with_no_tasks();
    $this->submit_submission();    

    $this->view_submitted_screen($this->securityArchitect, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->securityArchitect, "endorsement");
    $this->deny_submission($this->securityArchitect, "waiting_for_approval", "Waiting for approval");
  
    $this->view_submitted_screen($this->ciso, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->ciso, "endorsement");
    $this->deny_submission($this->ciso, "waiting_for_approval", "Waiting for approval");

    $this->view_submitted_screen($this->businessOwner, "waiting_for_approval", "Waiting for approval", true, false, "approval");
    $this->approve_submission($this->businessOwner, "approved", "Approved");
  }   

  /**
   * Check business owner also denying the change
   */
  public function test_business_owner_deny(): void {
    $this->create_submission_with_no_tasks();
    $this->submit_submission();    

    $this->view_submitted_screen($this->securityArchitect, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->securityArchitect, "endorsement");
    $this->deny_submission($this->securityArchitect, "waiting_for_approval", "Waiting for approval");
  
    $this->view_submitted_screen($this->ciso, "waiting_for_approval", "Waiting for approval", true, true, false);
    $this->assign_submission_to($this->ciso, "endorsement");
    $this->deny_submission($this->ciso, "waiting_for_approval", "Waiting for approval");

    $this->view_submitted_screen($this->businessOwner, "waiting_for_approval", "Waiting for approval", true, false, "approval");
    $this->deny_submission($this->businessOwner, "denied", "Not approved");
  }  
}
