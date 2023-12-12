<?php
namespace App\Objects;


class Questionnaire {
  protected $questions = null;
  public $errors = array();

  function __construct(String $questions) {
    // Data is the whole 
    $this->questions = json_encode($questions, true);
  }

  /**
   * Validate Business logic in our questionnaire text
   */
  public function validate() {
    $productNameCount = 0;

    foreach($this->questions as $question) {
      foreach($question["answerInputFields"] as $inputField) {
        $productNameCount = $inputField["productName"] == true ? $productNameCount + 1 : $productNameCount;
      }
    }
  }

}