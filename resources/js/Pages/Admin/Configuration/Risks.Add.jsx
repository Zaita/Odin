import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import ThemedButton from '@/Components/ThemedButton';

import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function AddRisk(props) {  
  console.log("Admin.Configuration.Risk.Add");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    console.log(`risks.add[${id}] = ${value}`);
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["name"],
  }

  let descriptionField = { 
    "label": "Description",
    "placeholder": "",
    "required": false,
    "value": userAnswers.current["description"]
  }
  
  function saveAnswersCallback() {
    SaveAnswers("admin.configuration.risk.create", setSaveOk, setSaveErrors, userAnswers.current)
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
          {/* Description */}
          <div className="w-full">
          <TextField field={descriptionField} value={descriptionField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="65px" camalCase runInit/>
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
    ["Risks", "admin.configuration.risks"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
