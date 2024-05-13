<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\HelpItem;

class HelpItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      // Delete all previous
      HelpItem::where('id', '>=', '0')->delete();

      $sortOrder = 0;
      HelpItem::create([
        "name" => "What is Odin?",
        "summary" => "Odin is a web application self-service entry point for a quality assurance lifecycle process.",
        "content" => "
        <p>Odin is a web application self-service entry point for a quality assurance lifecycle process. This tool collects relevant information about a delivery,
          determines the risk rating and generates the appropriate requirements. The tool tailors the list of requirements to the projects specific needs, without 
          providing unnecessary unrelated requirements. The process is derived from a security centric perspective, allowing the security teams and non-security 
          stakeholders (e.g., privacy, data, finance) to establish custom guidance and requirements as checklist items for all deliveries.</p>
        <p>&nbsp;</p>
        <p>Odin is used as a guide and reference for deliverying high quality outcomes, be they software release or entire projects. This encourages a security 
          mindset among project teams and can be used to easily track the completion of requirements for the project. Odin is a no-code solution, allowing quick 
          and easy deployment of workflows that support organisational delivery processes. You can be up and running in a few minutes with the pre-configured work-flows.</p>
        <p>&nbsp;</p>
        <p>Additionally, Odin comes pre-configured with:<br/>
        - Basic workflows to illustrate common scenarios including tasks covering basic concerns like privacy, security and data management<br/>        
        - Customisable approval flows with delegations<br/>        
        - Digital security risk assessment and control validation audit capability<br/>        
        - Usage Reporting<br/>
        </p>
        ",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "How does it work?",
        "summary" => "A user representing the change will create a submission in Odin and get assigned requirements as tasks.",
        "content" => "
        <p>Odin is a change management and approval system. A user representing the change can complete the entire change management process by engaging with Odin.
        </p>
        <p>&nbsp;</p>
        <p>The Odin process is a multi-stage process allowing for complete change management coverage. The change lifecycle includes:<br/>
        1. Entering basic information about the change.<br/>
        2. Getting assigned the appropriate requirements/tasks for the change.<br/>
        3. Completing the tasks and getting the required task approvals (e.g., privacy, procurement, legal).<br/>
        4. Submitting the change for approval.<br/>
        5. Receiving approval.<br/>
        </p>
        <p>&nbsp;</p>
        <p>Odin is designed to be the central authority on the change. Information regarding all requirements, and records of approval are all stored within Odin.
        Change records in Odin should server as an audit log for all changes within an organisation.</p>
        ",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Do approvals get stored in Odin?",
        "summary" => "Odin has support for task and submission approvals, including customised approval flows.",
        "content" => "
        <p>As Odin is designed to cover the entire change management lifecycle, it has been designed to support different levels of approval and user customised 
        approval flows. There are two types of approvals in Odin.</p>
        <p>&nbsp;</p>
        <p>A <b>Task Approval</b> is a required approval on a single task within a change submission. A change submission can have multiple tasks, and each task
        can be configured to have a required approver. A task configured with required approval will require a single approver from a group of Odin users configured
        in the Users->Groups section of the administration dashboard.</p>
        <p>A <b>Submission Approval</b> is an approval on the entire change submission. This can only be done once all tasks have been completed and/or approved. The
        entire change submission is sent for approval and enters a custom approval flow. Once the approval flow is complete, the change submission is marked as approved.</p>
        <p>&nbsp;</p>
        <p>There are some key things to consider when setting up tasks and custom approval flows:<br/>
        1. A task only needs to be approved by one person in the assigned group, but can be configured to notify all members.<br/>
        2. A submission cannot be sent for approval until all tasks are completed, and those requiring approval have been approved.<br/>
        3. Approvals on a submission can have multiple stages, with people either endorsing or approving the change.<br/>
        </p>
        ",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Can we have different change types and tasks?",
        "summary" => "Yes, the change types (pillars) and tasks are entirely customisable.",
        "content" => "
        <p>Odin supports completely customisable pillars and tasks. They are fully customisable through the administration dashboard with no code changes required. 
        Odin is a no-code change management system that allows change managers the ability to easily update and improve the change management process.</p>
        <p>&nbsp;</p>
        <p>The Odin change management process is designed to be iterative and updated as your organisation learns and enhances its processes.</p>        
        ",
        "sort_order" => $sortOrder++
      ]);
    }
}
