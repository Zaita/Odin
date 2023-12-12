<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Pillar;
use App\Models\Questionnaire;

class PillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $sortOrder = 0;
      /**
       * Risk Profile Pillar
       */
      $fileName = "storage/content/pillars/RiskProfile.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)));
      fclose($file);

      $qid = Questionnaire::where('name', 'Risk Profile')->first()->id;

      $p = new Pillar();
      $p->name = "Risk Profile";
      $p->caption = "Perform a standalone risk profile analysis";
      $p->icon = "message";
      $p->key_information = $json->questionnaire->keyInformation;
      $p->auto_approve = true;
      $p->auto_approve_no_tasks = true;
      $p->submission_expires = true;
      $p->expire_after_days = 7;
      $p->sort_order = $sortOrder++;
      $p->questionnaire_id = $qid;
      $p->save();
      printf("New Pillar created: %s with id: %d\n", $p->name, $p->id);

      /**
       * Proof of Concept Pillar
       */
      $fileName = "storage/content/pillars/ProofOfConcept.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)));
      fclose($file);

      $qid = Questionnaire::where('name', 'Proof of Concept')->first()->id;

      $p = new Pillar();
      $p->name = "Proof of Concept";
      $p->caption = "I want to trial a product for a shield period of time";
      $p->icon = "lightbulb";
      $p->key_information = $json->questionnaire->keyInformation;
      $p->auto_approve = true;
      $p->auto_approve_no_tasks = true;
      $p->submission_expires = true;
      $p->expire_after_days = 7;
      $p->sort_order = $sortOrder++;
      $p->questionnaire_id = $qid;
      $p->save();
      printf("New Pillar created: %s with id: %d\n", $p->name, $p->id);

      /**
       * Cloud Product onboarding
       */
      $fileName = "storage/content/pillars/CloudProductOnboarding.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)));
      fclose($file);

      $qid = Questionnaire::where('name', 'Cloud Product Onboarding')->first()->id;

      $p = new Pillar();
      $p->name = "Cloud Product Onboarding";
      $p->caption = "I want to use a cloud of SaaS product";
      $p->icon = "download";
      $p->key_information = $json->questionnaire->keyInformation;
      $p->auto_approve = true;
      $p->auto_approve_no_tasks = true;
      $p->submission_expires = true;
      $p->expire_after_days = 7;
      $p->sort_order = $sortOrder++;
      $p->questionnaire_id = $qid;
      $p->save();
      printf("New Pillar created: %s with id: %d\n", $p->name, $p->id);   

      /**
       * New Project or Product
       */
      $fileName = "storage/content/pillars/NewProjectOrProduct.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)));
      fclose($file);

      $qid = Questionnaire::where('name', 'New Project or Product')->first()->id;

      $p = new Pillar();
      $p->name = "New Project or Product";
      $p->caption = "I want to get my product/feature approved for release";
      $p->icon = "shield";
      $p->key_information = $json->questionnaire->keyInformation;
      $p->auto_approve = true;
      $p->auto_approve_no_tasks = true;
      $p->submission_expires = true;
      $p->expire_after_days = 7;
      $p->sort_order = $sortOrder++;
      $p->questionnaire_id = $qid;
      $p->save();
      printf("New Pillar created: %s with id: %d\n", $p->name, $p->id);   

      /**
       * New Project or Product
       */
      $fileName = "storage/content/pillars/ProductRelease.json";
      $file = fopen($fileName, "r") or die("Unable to open file!");
      $json = json_decode(fread($file,filesize($fileName)));
      fclose($file);

      $qid = Questionnaire::where('name', 'Product Release')->first()->id;

      $p = new Pillar();
      $p->name = "Product Release";
      $p->caption = "I want to release a change to an existing product";
      $p->icon = "bug";
      $p->key_information = $json->questionnaire->keyInformation;
      $p->auto_approve = true;
      $p->auto_approve_no_tasks = true;
      $p->submission_expires = true;
      $p->expire_after_days = 7;
      $p->sort_order = $sortOrder++;
      $p->questionnaire_id = $qid;
      $p->save();
      printf("New Pillar created: %s with id: %d\n", $p->name, $p->id);         

    }
}

