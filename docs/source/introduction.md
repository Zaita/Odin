# Introduction

## What is the Odin?
Let's start with what the Odin accomplishes. It simplifies your change management by funnelling change management processes through a single platform that has security, privacy and information management as core tenants. While many change management systems come from the perspective of rigid process, the Odin customises the processes and requirements based on the complexity of the change. A small change with a very low risk profile will take <5 minutes to complete, while a very complex and large change may have their submission open for many months spending a few minutes every few weeks updating it.

So what is the Odin? It's a web application that provides change management workflows for your delivery teams. It has been designed by security and software professionals to make the delivery lifecycle as low-touch and quick as possible.

A change leader (e.g., technical lead) can submit the change information through the Odin and it will automatically create the tasks required before going live. Many of the tasks can be completed within the Odin, without the need for another product. The tasks that need external information will store the outcomes, or links to outcomes, in the Odin for future reference.

Out of the box, the Odin comes with some basic configurations covering standard business domains (e.g., Privacy, Information Management, Security, Risk). But, it's an exceptionally easy system to configure with no requirement for the admins to have any development or coding knowledge. 

In the real world, the Odin has been used to achieve ISO27001 compliance with minimal changes to the change and software development experience. The Odin is ideal for environments and organisations that want a fast, low touch, and easy to follow change management process.

Some of the things that Odin can do for you:
- Greatly speed up your change management
- Remove the need for change approval meetings 
- Ensure multiple stakeholders (e.g., security, privacy, information management) are represented and informed
- Provide a single source of truth

## Goals
The goals of the Odin are:
1. Self-Service : Provide self service tool for Project Lead to get a checklist of requirements related to their project.
2. Specific : Project Lead or Developer can identify the components relevant to their delivery and avoid unneccessary effort.
3. Standardise : Deliveyr teams will work through a single standardised process, ensuring consistent quality outcomes.
4. Customisable : The Odin provides a no-code ability to configure all workflows, requirements and risk assessment ratings.

## General Usage
It is expected that delivery teams will use the Odin:
1. At the begining of the project or delivery, have the delivery lead complete the Odin form to get a list of requirements.
2. During the design of the delivery, use the information on controls and organisational technical patterns from the Odin to "design by numbers".
3. As the delivery is being built and tested, update the risk assessment (if applicable) with the implementation status of controls.
4. When the delivery is ready to go in to product, submit the Odin submission for approval to all relevant stakeholders.

## The User Flow
The user flow can be summarised as: `Pillar -> Tasks -> Approval`. The Odin creates a submission object in the backend and this moves through different states depending on where in the lifecycle it is.

When the user clicks on a pillar from the dashboard, they will be asked to complete a questionnaire in the Odin. The Odin creates a new submission and marks it as "in progress". Once the user completes this initial set of questions and submits it, the Odin will mark it as "submitted" and determine the required tasks for this submission. Tasks are assigned based on the answers to the initial questionnaire.

Once the user completes all tasks, the Odin can be submitted for approval. The submission is marked as "waiting for security architect" and an email is sent to members of the "Odin-SecurityArchitect" Odin group. A member of this group will look at the submission and click "assign to me". Once they have completed their review they will click "approved" and the submission will be marked as "waiting for approval".

Emails will be sent to members of the "Odin-CISO" Odin group and to the email nominated as the business owner in the pillar questionnaire. A member of the CISO group will look at the submission and provide a recommendation approval by clicking "approve". This will not change the status of the submission. The business owner will review the submission and click "approve", this will mark the entire submission as "approved" and the user/submitter will receive an email notifying them that the submission has been approved.

A Short-breakdown of user flow would look like:

    1. A user enters the Odin landing page
    2. The user selects a pillar for their type of deliverable
    3. The Odin displays a "key information" page
    4. The user clicks "Start" to begin the submission.
    5. The user (submitter) answers the questions within that pillar
    6. The submitter reviews their responses before submitting to the Odin
    7. The Odin processes the responses and creates the appropriate Tasks to be completed
    8. The submitter completes the tasks through the Odin
    9. Tasks that require approval when completed by the submitter will email their approval groups to be approved
    10. Once all tasks have been approved/completed, the Odin enables the "Submit for Approval" button
    11. The submitter then submits their submission for approval
    12. Members of the Security Architect group get an email asking them to review and approve
    13. Once Security Architect approves the submission
    14. The Odin emails the Business Owner and Chief Information Security Officer asking them to review and approve
    15. The Business Owner approves the submission
    16. The submitter gets an email notifying them of the approved submission
