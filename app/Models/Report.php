<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory;

    public $header = array();
    public $rows = array();

    protected $fillable = [
      "name",
    ];

    public function execute() {
      if ($this->name == "Number of non-expired submissions in each pillar") {
        $this->SubmissionsPerPillar();
      } else if ($this->name == "Number of submissions in each pillar per year") {
        $this->SubmissionsByPillarPerYear();
      } else if ($this->name == "Number of submissions in each pillar per month and year") {
        $this->SubmissionsByPillarPerYearAndMonth();
      } else if ($this->name == "Number of submissions in each pillar per year and month") {
        $this->SubmissionsByPillarPerYearAndMonth();
      } else if ($this->name == "Number of submissions approved by each member of SecurityArchitects group") {
        $this->SubmissionsApprovedByEachSecurityArchitect();
      } else if ($this->name == "Number of submissions approved by each member of SecurityArchitects group per year and pillar") {
        $this->SubmissionsApprovedByEachSecurityArchitectPerYearAndPillar();
      } else if ($this->name == "Number of submissions approved by each member of SecurityArchitects group per month/year and pillar") {
        $this->SubmissionsApprovedByEachSecurityArchitectPerYearMonthAndPillar();
      } else if ($this->name == "Number of submissions approved by each member of SecurityArchitects group per year/month and pillar") {
        $this->SubmissionsApprovedByEachSecurityArchitectPerYearMonthAndPillar();
      } else if ($this->name == "Number of tasks completed per year by type") {
        $this->NumberOfTasksCompletedPerYearByType();
      }
    }

    public function SubmissionsPerPillar() {
      $submissions = DB::table('submissions')
      ->select('pillar_name', DB::raw('count(*) as count'))
      ->groupBy(['pillar_name'])
      ->get();

      $this->header = ["Pillar", "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->pillar_name, $r->count]);
      }  
    }

    public function SubmissionsByPillarPerYear() {
      $submissions = DB::table('submissions')
        ->select(DB::raw('DATE_FORMAT(created_at, "%Y") as date'), 'pillar_name', DB::raw('count(*) as count'))
        ->groupBy(['date', 'pillar_name'])
        ->get();

      $this->header = ["Year", "Pillar", "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->date, $r->pillar_name, $r->count]);
      }      
    }

    public function SubmissionsByPillarPerYearAndMonth() {
      $submissions = DB::table('submissions')
        ->select(DB::raw('DATE_FORMAT(created_at, "%Y") as year'), DB::raw('DATE_FORMAT(created_at, "%m") as month'), 'pillar_name', DB::raw('count(*) as count'))
        ->groupBy(['year', 'month', 'pillar_name'])
        ->get();

      $this->header = ["Year", "Month", "Pillar", "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->year, $r->month, $r->pillar_name, $r->count]);
      }      
    }

    public function SubmissionsApprovedByEachSecurityArchitect() {
      $submissions = DB::table('submission_approval_flow_stages')
        ->select('approved_by_user_name', DB::raw('count(id) as count'))
        ->where(['target' => "Security Architect"])
        ->whereNot(['approved_by_user_name' => 'null'])
        ->groupBy(['approved_by_user_name'])
        ->get();

      $this->header = ["Security Architect", "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->approved_by_user_name, $r->count]);
      }      
    }

    public function SubmissionsApprovedByEachSecurityArchitectPerYearAndPillar() {
      $submissions = DB::table('submission_approval_flow_stages')
        ->join('submissions', 'submissions.id', '=', 'submission_approval_flow_stages.submission_id')
        ->select('approved_by_user_name', DB::raw('DATE_FORMAT(submissions.created_at, "%Y") as year'), 'pillar_name', DB::raw('count(submission_approval_flow_stages.id) as count'))
        ->where(['target' => "Security Architect"])
        ->whereNot(['approved_by_user_name' => 'null'])
        ->groupBy(['approved_by_user_name', 'pillar_name', 'year'])
        ->get();

      $this->header = ["Security Architect", "Year", "Pillar Name",
       "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->approved_by_user_name, $r->year, $r->pillar_name, $r->count]);
      }      
    }

    public function SubmissionsApprovedByEachSecurityArchitectPerYearMonthAndPillar() {
      $submissions = DB::table('submission_approval_flow_stages')
        ->join('submissions', 'submissions.id', '=', 'submission_approval_flow_stages.submission_id')
        ->select('approved_by_user_name', DB::raw('DATE_FORMAT(submissions.created_at, "%Y") as year'), 
          DB::raw('DATE_FORMAT(submissions.created_at, "%m") as month'),
          'pillar_name', DB::raw('count(submission_approval_flow_stages.id) as count'))
        ->where(['target' => "Security Architect"])
        ->whereNot(['approved_by_user_name' => 'null'])
        ->groupBy(['approved_by_user_name', 'pillar_name', 'year', 'month'])
        ->get();

      $this->header = ["Security Architect", "Year", "Month", "Pillar Name",
       "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->approved_by_user_name, $r->year, $r->month, $r->pillar_name, $r->count]);
      }      
    }    

    public function NumberOfTasksCompletedPerYearByType() {
      $submissions = DB::table('task_submissions')
        ->select('name', DB::raw('DATE_FORMAT(task_submissions.created_at, "%Y") as year'), 
          DB::raw('count(id) as count'))
        // ->where(['status' => "approved"])
        // ->orWhere(['status' => "completed"])
        ->groupBy(['name', 'year'])
        ->get();

      $this->header = ["Name", "Year", "Count"];
      foreach( $submissions as $r ) {
        array_push($this->rows, [$r->name, $r->year, $r->count]);
      }        
    }
    
}
