<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Questionnaire;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $fileName = "storage/content/pillars/ProofOfConcept.json";
      $pocFile = fopen($fileName, "r") or die("Unable to open file!");

      $stringValue = fread($pocFile,filesize($fileName));
      $stringValue = str_replace("questionHeading", "heading", $stringValue);
      $stringValue = str_replace("isBusinessOwner", "businessOwner", $stringValue);
      $stringValue = str_replace("isProductName", "productName", $stringValue);
      $stringValue = str_replace("isTicketLink", "ticketUrl", $stringValue);
      $stringValue = str_replace("isReleaseDate", "releaseDate", $stringValue);

      $json = json_decode($stringValue);
      fclose($pocFile);

      $q = new Questionnaire();
      $q->importFromJson($json->questionnaire);

      /**
       * New Project or Product Questionnaire
       */
      $fileName = "storage/content/pillars/NewProjectOrProduct.json";
      $pocFile = fopen($fileName, "r") or die("Unable to open file!");

      $stringValue = fread($pocFile,filesize($fileName));
      $stringValue = str_replace("questionHeading", "heading", $stringValue);
      $stringValue = str_replace("isBusinessOwner", "businessOwner", $stringValue);
      $stringValue = str_replace("isProductName", "productName", $stringValue);
      $stringValue = str_replace("isTicketLink", "ticketUrl", $stringValue);
      $stringValue = str_replace("isReleaseDate", "releaseDate", $stringValue);

      $json = json_decode($stringValue);
      fclose($pocFile);

      $q = new Questionnaire();
      $q->importFromJson($json->questionnaire);

      /**
       * Cloud Product Onboarding
       */
      $fileName = "storage/content/pillars/CloudProductOnboarding.json";
      $pocFile = fopen($fileName, "r") or die("Unable to open file!");

      $stringValue = fread($pocFile,filesize($fileName));
      $stringValue = str_replace("questionHeading", "heading", $stringValue);
      $stringValue = str_replace("isBusinessOwner", "businessOwner", $stringValue);
      $stringValue = str_replace("isProductName", "productName", $stringValue);
      $stringValue = str_replace("isTicketLink", "ticketUrl", $stringValue);
      $stringValue = str_replace("isReleaseDate", "releaseDate", $stringValue);

      $json = json_decode($stringValue);
      fclose($pocFile);

      $q = new Questionnaire();
      $q->importFromJson($json->questionnaire);

      /**
       * Risk Profile
       */
      $fileName = "storage/content/pillars/RiskProfile.json";
      $pocFile = fopen($fileName, "r") or die("Unable to open file!");

      $stringValue = fread($pocFile,filesize($fileName));
      $stringValue = str_replace("questionHeading", "heading", $stringValue);
      $stringValue = str_replace("isBusinessOwner", "businessOwner", $stringValue);
      $stringValue = str_replace("isProductName", "productName", $stringValue);
      $stringValue = str_replace("isTicketLink", "ticketUrl", $stringValue);
      $stringValue = str_replace("isReleaseDate", "releaseDate", $stringValue);
      $stringValue = str_replace("RiskQuestionnaire", "risk_questionnaire", $stringValue);

      $json = json_decode($stringValue);
      fclose($pocFile);

      $q = new Questionnaire();
      $q->importFromJson($json->questionnaire);

      /**
       * Risk Profile
       */
      $fileName = "storage/content/pillars/ProductRelease.json";
      $pocFile = fopen($fileName, "r") or die("Unable to open file!");

      $stringValue = fread($pocFile,filesize($fileName));
      $stringValue = str_replace("questionHeading", "heading", $stringValue);
      $stringValue = str_replace("isBusinessOwner", "businessOwner", $stringValue);
      $stringValue = str_replace("isProductName", "productName", $stringValue);
      $stringValue = str_replace("isTicketLink", "ticketUrl", $stringValue);
      $stringValue = str_replace("isReleaseDate", "releaseDate", $stringValue);

      $json = json_decode($stringValue);
      fclose($pocFile);

      $q = new Questionnaire();
      $q->importFromJson($json->questionnaire);
    }
}
