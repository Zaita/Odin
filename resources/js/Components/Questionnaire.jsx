import React, { useState, useEffect, useRef } from 'react';
import CheckBoxOutlineBlankIcon from '@mui/icons-material/CheckBoxOutlineBlank';

import Menu from './Questionnaire/Menu';
import Form from "./Questionnaire/Form"

export default function Questionnaire(props) {
  const currentQuestionIndex = useRef(-1);

  const [menu, setMenu] = useState("");
  const [currentQuestion, setCurrentQuestion] = useState("");
  const [questions, setQuestions] = useState(props.questionData);
  const [answers, setAnswers] = useState(props.answerData);

  // Init our data structures
  useEffect(() => {
    console.log("Effect Called");

    let menuItems = [];
    {questions.map((question, index) => {
      menuItems.push([<CheckBoxOutlineBlankIcon style={{width: "14px"}}/>, index+1, question.title]);
      })
    } 
    console.log(`Setting menu to new value with ${menuItems.length} items`);
    setMenu(menuItems); 
  }, []);

  /**
   * Function to handle callback from the menu or question
   * @param {} newIndex 
   */
  function gotoQuestion(title) {
    console.log(`Moving forward to question: ${title}`);       
    let startSkip = false;
    questions.map(
      (question, index) => 
        {
          // First, check if this is the question we're looking for:
          if (question.title == title) {
            console.log("Found question, setting it up");
            setCurrentQuestion(question);
            currentQuestionIndex.current = index; 
            return;   
          }
        }
      );
    }

  function nextQuestion() {    
    console.log("Questionnaire.nextQuestion() called"); 
    currentQuestionIndex.current = currentQuestionIndex.current + 1;   
    setCurrentQuestion(questions[currentQuestionIndex.current]);
  }

  /**
   * 
   **/ 
  if (currentQuestionIndex.current == -1) {
    console.log("currentQuestionIndex.current");
    if (answers.last_question != null && answers.last_question != "") {
      gotoQuestion(answers.last_question);
    } else {
      gotoQuestion(questions[0].title);
    }
  }

  /**
   * Return our render
   */
  console.log("Render Called");
  return ( 
    <div id="inner_content">
      <div id="smaller_title">Questions</div>
      <div id="inprogress_content">
        <Menu callBack={gotoQuestion} menu={menu} questions={questions} answers={answers} currentQuestion={currentQuestion} {...props}/>     
        <Form gotoQuestionCallback={gotoQuestion} 
          nextQuestionCallback={nextQuestion} 
          updateAnswersCallback={setAnswers}
          question={currentQuestion}
          questionIndex={currentQuestionIndex.current} 
          answers={answers} 
          updateRoute={props.updateRoute}
          uuid={props.uuid}
          {...props}/>
      </div>
    </div>
  );
}