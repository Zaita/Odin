import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';

import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function PillarAddQuestion(props) {  
  console.log("Admin.Content.Pillars.Questions.Add");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
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
    SaveAnswersWithId("admin.content.pillar.question.create", props.pillar.id, setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
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
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Create"/>
        <p>{saveOk}</p>
      </div> 
      </>
    );
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
