import React, { useRef, useState } from 'react';

import TextField from '@/Components/TextField';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import RichTextAreaField from '@/Components/RichTextAreaField';

export default function QuestionEdit(props) {  
  let [errors, setErrors] = useState("");
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

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
  
  function saveCallback() {
    userAnswers.current["questionId"] = props.question.id;
    SaveAnswersWithId(props.saveRoute, props.saveRouteParameters, setSaveOk, setErrors, userAnswers.current)
  }

  return (
    <>
    <div className="flex">
      <div className="overflow-y-auto w-5/6">
        {/* Title */}
        <div className="w-full">
        <TextField field={nameField} value={nameField.value} submitCallback={saveCallback}
              handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase/>
        </div>
        {/* Question Heading */}
        <div className="w-full">
        <TextField field={headingField} value={headingField.value} submitCallback={saveCallback}
              handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} height="65px" camalCase/>
        </div>
        {/* Description */}
        <div className="w-full">
        <RichTextAreaField field={descriptionField} value={descriptionField.value} submitCallback={saveCallback}
              handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} height="200px" camalCase/>
        </div>           
      </div>
    </div>
    <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
      <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/></div>
      <div className="pl-2 font-bold">{saveOk}</div>
      <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
    </div> 
    </>
  );
}
