import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';

import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function EditRisk(props) {  
  console.log("Admin.Content.Pillars.Edit");
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
    "value": props.risk ? props.risk.name : ""
  }

  let descriptionField = { 
    "label": "Description",
    "placeholder": "",
    "required": false,
    "value": props.risk?.description ? props.risk.description : ""
  }

  function saveAnswersCallback() {
    SaveAnswersWithId("admin.configuration.risk.save", props.risk.id, setSaveOk, setSaveErrors, userAnswers.current)
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

  let topMenuItems = []

  let breadcrumb = [
    ["Risks", "admin.configuration.risks"],
    [props.risk.name, "admin.configuration.risk.edit", props.risk.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
