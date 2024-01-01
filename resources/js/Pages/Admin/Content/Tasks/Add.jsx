import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';

import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function TasksAdd(props) {  
  console.log("Admin.Content.Pillars.Add");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    console.log(`pillar.edit[${id}] = ${value}`);
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["name"],
  }
  
  let typeField = {
    "label": "Type",
    "required" : true,
    "value": userAnswers.current["type"] ? userAnswers.current["type"] : "questionnaire"
  }
  let typeOptions = ["questionnaire", "risk_questionnaire", "risk_assessment", "control_validation_audit"];

  function saveAnswersCallback() {
    SaveAnswers("admin.content.task.create", setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Title */}
          <div className="w-full">
          <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>
          {/* Type */}
          <div className="w-full">
            <DropdownField field={typeField} value={typeField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={typeOptions} camalCase runInit/>
          </div>           
        </div>
      </div>
      <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/>
        <p>{saveOk}</p>
      </div> 
      </>
    );
  }

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
