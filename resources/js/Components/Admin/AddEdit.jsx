import React, { useRef, useState } from 'react';

import ColorField from '../ColorField';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import RichTextAreaField from '@/Components/RichTextAreaField';

import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function AddEdit(props) {
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);
  let inputs = [];

  // Record the value of an input field that has changed
  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  // Save our answers
  function saveAnswersCallback() {
    SaveAnswersWithId(props.saveRoute, props.saveRouteParameters, setSaveOk, setSaveErrors, userAnswers.current)
  }
  
  /**
   * Build our input list to render
   */
  props.inputs.map((inputField, index) => {
    switch(inputField.type) {
      case "text": 
      case "url":
      case "email":        
        inputs.push(<TextField field={inputField} value={inputField.value} submitCallback={saveAnswersCallback}
          handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit/>)
        break;
      case "textarea":
        inputs.push(<TextAreaField field={inputField} value={inputField.value} 
          handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit/>)
        break;          
      case "date":          
        inputs.push(<DatePickerField field={inputField} value={inputField.value} 
          handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit/>)
        break;  
      case "color":
        inputs.push(<ColorField field={inputField} value={inputField.value} 
          handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit/>)
        break;
      case "richtextedit":
      case "richtexteditor":
      case "richtext":
        inputs.push(<RichTextAreaField field={inputField} value={inputField.value} submitCallback={saveAnswersCallback}
          handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" dbFormat runInit/>)
        break;
    }
  });

  // Render
  return (
    <>
    <div className="flex">
      <div className="overflow-y-auto w-5/6">
        { inputs.map((input, index) => 
          <div className="w-full" key={index}>
            {input}
          </div>
        )}        
      </div>
    </div>
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/>
      <p>{saveOk}</p>
    </div> 
    </>
  );
}