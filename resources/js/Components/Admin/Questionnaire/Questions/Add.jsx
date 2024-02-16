import React, { useRef, useState } from 'react';

import TextField from '@/Components/TextField';
import ThemedButton from '@/Components/ThemedButton';

import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import RichTextAreaField from '@/Components/RichTextAreaField';

export default function QuestionAdd(props) {  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "TItle",
    "placeholder": "title",
    "required": true,
    "value": userAnswers.current["title"] ? userAnswers.current["title"] : "",
  }

  let headingField = { 
    "label": "Heading",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["heading"]
  }
  let descriptionField = { 
    "label": "Description",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["description"]
  }
  
  function saveAnswersCallback() {
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
        <RichTextAreaField field={descriptionField} value={descriptionField.value} submitCallback={saveAnswersCallback}
              handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" camalCase/>
        </div>           
      </div>
    </div>
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Create"/>
      <p>{saveOk}</p>
    </div> 
    </>
  );
}
