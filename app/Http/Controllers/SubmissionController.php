<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

use App\Models\Configuration;
use App\Models\Pillar;
use App\Models\Questionnaire;
use App\Models\Submission;

class SubmissionController extends Controller
{
    /**
     * This method will take the pillarId and create a new submission
     * with the contents of the questionnaire and pillar. Because we bind a 
     * submission to the version of the pillar at the point of starting it,
     * well need to copy all that information into the submission entry.
     * 
     * Once this is done, we route to the inProgress page with the new UUID
     * of the submission so that the questions can be displayed to the user.
     */
    public function start(Request $request, $pillarId) {
      $pillar = Pillar::where('id', $pillarId)->first();
      if (is_null($pillar)) {
        Log::emergency("Could not find a pillar with id ${pillarId} to create a new submission");
      }
      
      $user = $request->user();
      $s = new Submission();
      $s->user_id = $user->id;
      $s->submitter_name = $user->name;
      $s->submitter_email = $user->email;
      $s->pillar_name = $pillar->name;
      $s->questionnaire_data = $pillar->questions;
      $s->save();

      return Redirect::route('submission.inprogress', ['uuid' => $s->uuid]);   
    }

    /**
     * This method will load the questionnaire submission for the current uuid.
     */
    public function view(Request $request, $uuid) {
      $submission = Submission::where('uuid', $uuid)->first();
      if ($submission->status == "in_progress") {
        return Redirect::route('submission.inprogress', ['uuid' => $uuid]);   
      } else if ($submission->status == "submitted") {
        return Redirect::route('submission.submitted', ['uuid' => $uuid]);   
      }
    }
    /**
     * This method will load the questionnaire submission for the current uuid.
     */
    public function inProgress(Request $request, $uuid) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      $submission = Submission::where('uuid', $uuid)->first();

      return Inertia::render('Submission/InProgress', [
        'siteConfig' => $config,
        'submission' => $submission,
      ]);  
    }

    /**
     * Update our submission with new answers from the user.
     */
    public function update(Request $request, $uuid) {
      Log::Info("--> NEW REQUEST **********************************");

      $config = json_decode(Configuration::GetSiteConfig()->value);
      $submission = Submission::where('uuid', $uuid)->first();
      
      $errors = array();

      // Populate an array with the user's answers
      $actionValue = null;
      $userAnswers = $request->input('answers', []);
      $newAnswerValues = array();
      foreach($userAnswers as $field => $value) {
        $newAnswerValues[$field] = $value; 
        // if the answer has a button action, we need to
        // check for a goto outcome and update the not_applicable
        // answers
        if ($field == "action") {
          $actionValue = $value; // Answer has an action type
        }
      }

      // Set the answers on our submission
      $currentQuestion = $request->input('question', '');
      $lastQuestion = $currentQuestion;
      $submissionQuestions = json_decode($submission->questionnaire_data);
      $submissionAnswers = json_decode($submission->answer_data);
      if ($submission->validateAnswers($errors, $currentQuestion, $submissionQuestions, $newAnswerValues)) {
        $submission->setAnswers($submissionAnswers, $currentQuestion, $newAnswerValues); // Modified $submissionAnswers

        /**
         * Now we need to check if we're jumping forward more than one question. This is caused
         * by the goto action on a button.
         */
        if ($actionValue != null) {
          $questionnaireData = json_decode($submission->questionnaire_data);
          foreach ($questionnaireData as $question) {
            if ($question->title == $currentQuestion) {
              foreach($question->answerActionFields as $actionField) {
                if ($actionField->label == $actionValue) {
                  if ($actionField->actionType != "goto") {
                    break 2; // Stop both loops
                  }
                  // We have a go to
                  $submission->handleGoto($submissionAnswers, $currentQuestion, $actionField->gotoQuestionTitle);
                  $lastQuestion =  $actionField->gotoQuestionTitle;
                }
              }
              break;
            }
          }
        }
        
        // Update last question and save our submission
        $submissionAnswers->last_question = $lastQuestion;
        $submission->answer_data = json_encode($submissionAnswers);
        $submission->save();
      } // if ($submission->validateAnswers($errors, $currentQuestion, $newAnswerValues)

      // If we answered last question, go to review.
      if (count($errors) == 0 && $submission->isLastQuestion($currentQuestion, $submissionQuestions)) {
        Log::Info("Redirecting");
        return Redirect::route('submission.review', ['uuid' => $submission->uuid]);
      } else {
        return Inertia::render('Submission/InProgress', [
          'siteConfig' => $config,
          'submission' => $submission,
          'errors' => $errors
        ]);  
      }
    }

    /**
     * This method will load the questionnaire submission for the current uuid.
     */
    public function review(Request $request, $uuid) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      $submission = Submission::where('uuid', $uuid)->first();

      return Inertia::render('Submission/Review', [
        'siteConfig' => $config,
        'submission' => $submission,
      ]);  
    }

    /**
     * This method will load the questionnaire submission for the current uuid.
     */
    public function submit(Request $request, $uuid) {
      $config = json_decode(Configuration::GetSiteConfig()->value);
      $submission = Submission::where('uuid', $uuid)->first();
      $submission->status = "submitted";
      $submission->save();

      return Inertia::render('Submission/Submitted', [
        'siteConfig' => $config,
        'submission' => $submission,
      ]);  
    }

      /**
       * 
       */
      public function submitted(Request $request, $uuid) {
        $config = json_decode(Configuration::GetSiteConfig()->value);
        $submission = Submission::where('uuid', $uuid)->first();
        $submission->status = "submitted";
        $submission->save();
  
        return Inertia::render('Submission/Submitted', [
          'siteConfig' => $config,
          'submission' => $submission,
        ]);  
      }
}
