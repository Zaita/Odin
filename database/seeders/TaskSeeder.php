<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Task;
use App\Models\Questionnaire;

class TaskSeeder extends Seeder
{
  protected  $sortOrder = 0;

  protected function load_task($name) {
    $fileName = "storage/content/tasks/${name}.json";
    $file = fopen($fileName, "r") or die("Unable to open file!");
    $json = json_decode(fread($file,filesize($fileName)), true);
    fclose($file);

    $p = Task::firstOrNew(["name" => $json["name"]]);
    $p->importFromJson($json);
    $p->sort_order = $this->sortOrder++;
    $p->save();
  }

  /**
   * Run the database seeds.
   */
  public function run(): void {
    $this->load_task("PenetrationTest");    
    $this->load_task("PrivacyThresholdAssessment");
    $this->load_task("ReleaseNotes");  
    $this->load_task("WebSecurityConfiguration"); 
    $this->load_task("PCI_DSSAssessment"); 
    $this->load_task("OneQuestionTask"); 
    $this->load_task("OneQuestionTask"); 
    $this->load_task("DSRA"); 
  }
}

