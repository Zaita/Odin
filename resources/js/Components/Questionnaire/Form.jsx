import React, { useState, useEffect, createContext, useContext, useRef, Component } from 'react';
import { router } from '@inertiajs/react';
import ErrorOutlineIcon from '@mui/icons-material/ErrorOutline';
import "react-datepicker/dist/react-datepicker.css";

import TextField from '../TextField';
import TextAreaField from '../TextAreaField';
import DatePickerField from '../DatePickerField';
import Form_CheckBox from './Form.CheckBox';
import ThemedButton from '../ThemedButton';
import Form_RadioButton from './Form.RadioButton';

export default function Questionnaire_Form(props) { 
  let userAnswers = useRef({});
  let [saveErrors, setSaveErrors] = useState("");
  let inputs = [];  

  // Find the answer data for this specific form 
  let answerData = null;
  props.answers.answers.map((answer) => {
    if (answer.question == props.question.title) {
      answerData = answer;
    }
  });

  /**
   * Store the values from our field in the answers:useRef() so we can
   * access it for submitting to the backend on each question save.
   * @param {Field id} id 
   * @param {Field value} value 
   */
  function handleChange(id, value) {
    userAnswers.current[id] = value;
  };
  function handleSubChange(id, subId, value) {
    if (userAnswers.current[id] == undefined) {
      userAnswers.current[id] = {};
    }
    userAnswers.current[id][subId] = value;
  };


  /**
   * Save the answers. This is called when a question in our submission is completed.
   * We'll send the details to the back end so we can save progress as the user
   * works through their submission
   * @param {Function to call on success} successCallback 
   */
  function saveAnswers(successCallback) {
    console.log("Form.saveAnswers()");
    console.log(userAnswers.current);
    router.visit(route(props.updateRoute, [props.uuid] ), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        data: {
          question: props.question.title,
          answers:{...userAnswers.current}
        },
        onSuccess: (page) => {
          console.log("Saved Successfully");
          userAnswers.current = []; // clear these for next form
          if (page.props.answer_data) {
            props.updateAnswersCallback(JSON.parse(page.props.answer_data));
          }
          setSaveErrors(null);
          successCallback();
        },
        onError: (errors) => {
          console.log("saveAnswers Failed");            
          console.log(errors);
          setSaveErrors(errors);
        },
    })
  }
 
  /**
   * Save our answers passing in the nextQuestionCallback
   * as a parameter to be called on success
   */
  function nextQuestion() {    
    saveAnswers(props.nextQuestionCallback);      
  }

  /**
   * Save our answers passing in the gotoQuestioncallback
   * as a parameter to be called on success.
   * 
   */
  function gotoQuestion(questionTitle) {
    saveAnswers(function() { props.gotoQuestionCallback(questionTitle) });
  }
 
  /**
   * Pass in the label of the field we want to get the answers for.
   * Then search for it in the answers for this question.
   * @param {String} fieldLabel 
   * @returns 
   */
  function getAnswer(fieldLabel) {
    console.log(`Form.getAnswer(${fieldLabel})`);
    console.log(answerData.data);
    let result = "";
    if (answerData != null && answerData.data && answerData.data.some) {
      answerData.data.some((answer) => { 
        if (answer.field == fieldLabel) {
          result = answer.value;
          return;
        }
      });
    }
    return result != "" ? result : userAnswers.current[fieldLabel];
  }

  /**
   * Determine if the action button is selected or not. This will
   * change the colour of the button so it's obvious to the user.
   */
  function isActionSelected(actionLabel) {
    let result = false;
    if (answerData != null && answerData.data && answerData.data.some) {
      answerData.data.some((answer) => { 
        if (answer.field == "action" && answer.value == actionLabel) {
          result = true
          return;
        }
      });
    }
    return result;    
  }

  /**
   * 
   */
  if (props.question.input_fields != null) {
    props.question.input_fields.map((inputField, index) => {
      let fieldKey = `fk_${props.question.title}_${inputField.label}`;
      switch(inputField.input_type) {
        case "text": 
        case "url":
        case "email":        
          inputs.push([<TextField field={inputField} value={getAnswer(inputField.label)} submitCallback={nextQuestion}
            handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} runInit/>, 
            fieldKey])
          break;
        case "richtext":
        case "richtexteditor":
        case "textarea":
          inputs.push([<TextAreaField field={inputField} value={getAnswer(inputField.label)} 
            handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} runInit/>, fieldKey])
          break;          
        case "date":                  
        case "release_date":  
        case "release date":
          inputs.push([<DatePickerField field={inputField} value={getAnswer(inputField.label)} 
            handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} runInit/>, fieldKey])
          break;   
        case "checkbox":
          inputs.push([<Form_CheckBox field={inputField} values={getAnswer(inputField.label)}
            handleChange={handleSubChange} errors={saveErrors} siteConfig={props.siteConfig} runInit/>, fieldKey])
          break;
          case "radio":
            inputs.push([<
              Form_RadioButton field={inputField} value={getAnswer(inputField.label)}
              handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} runInit/>, fieldKey])
            break;          
      }
    });
  }

  /**
   * 
   */
  let actions = [];
  let defaultAction = "";
  if (props.question.action_fields != null) {
    props.question.action_fields.map((actionField) => {
      switch(actionField.action_type) {
        case "continue":
          actions.push(<ThemedButton siteConfig={props.siteConfig} handleChange={handleChange} onClick={nextQuestion} children={actionField.label} selected={isActionSelected(actionField.label)} />);
          break;
        case "goto":
          actions.push(<ThemedButton siteConfig={props.siteConfig} handleChange={handleChange} onClick={() => gotoQuestion(actionField.goto_question_title)} children={actionField.label} selected={isActionSelected(actionField.label)}/>);
          break;          
      }
    });
  } 
  if (actions.length == 0) {
    // Default action to continue to next question
    defaultAction = (<ThemedButton siteConfig={props.siteConfig} onClick={nextQuestion} children="Next"/>);
  }

  // On a re-render we want to clear userAnswers
  userAnswers.current = [];

  return (
    <div id="inprogress_right_panel" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
      <div id="question_heading" style={{ color: props.siteConfig.theme_header_color }}>{props.questionIndex+1}. {props.question.heading}</div>
      <div id="question_description" dangerouslySetInnerHTML={{__html: props.question.description}} />
      {inputs.map((element, index) => <div key={element[1]}>{element[0]}</div>)}      
      <div id="actions" className="flex">
        {actions.map((element, index) => <div key={index} className="float-left pr-2 mb-20">{element}</div>)}   
      </div>
      <div id="save_message" className="mt-2"><ErrorOutlineIcon style={{ color: props.siteConfig.theme_header_color }}/> Your answers will be saved when you continue to the next question.</div>  
      <div id="default_action">{defaultAction}</div>
  </div>
  );
}