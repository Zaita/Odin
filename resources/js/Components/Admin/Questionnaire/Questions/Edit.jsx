import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';

import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function QuestionEdit(props) {  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  function getValue(targetProp, label) {
    if (userAnswers.current[label] == null || userAnswers.current[label] == "") 
      userAnswers.current[label] = targetProp;
    return userAnswers.current[label] ? userAnswers.current[label] : (targetProp ?  targetProp : "");
  }

  let nameField = { 
    "label" : "TItle",
    "placeholder": "title",
    "required": true,
    "value": getValue(props.question?.title, "title")
  }

  let headingField = { 
    "label": "Heading",
    "placeholder": "",
    "required": true,
    "value": getValue(props.question?.heading, "heading")
  }

  let descriptionField = { 
    "label": "Description",
    "placeholder": "",
    "required": true,
    "value": getValue(props.question?.description, "description")
  }
  
  function saveAnswersCallback() {
    userAnswers.current["questionId"] = props.question.id;
    SaveAnswersWithId(props.saveRoute, props.saveRouteParameters, setSaveOk, setSaveErrors, userAnswers.current)
  }

  return (
    <>
    <div className="flex">
      <div className="overflow-y-auto w-5/6">
        {/* Title */}
        <div className="w-full">
        <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
              handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
        </div>
        {/* Question Heading */}
        <div className="w-full">
        <TextField field={headingField} value={headingField.value} submitCallback={saveAnswersCallback}
              handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="65px" camalCase/>
        </div>
        {/* Description */}
        <div className="w-full">
        <TextAreaField field={descriptionField} value={descriptionField.value} submitCallback={saveAnswersCallback}
              handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" camalCase/>
        </div>           
      </div>
    </div>
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Update"/>
      <p>{saveOk}</p>
    </div> 
    </>
  );
}
