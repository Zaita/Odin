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
}
