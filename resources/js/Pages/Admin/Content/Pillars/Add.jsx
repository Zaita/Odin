import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';

import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function EditPillars(props) {  
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

  let captionField = { 
    "label": "Caption",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["caption"]
  }
  let keyInformationField = { 
    "label": "Key Information",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["key_information"]
  }
  
  let typeField = {
    "label": "Type",
    "required" : true,
    "value": userAnswers.current["type"] ? userAnswers.current["type"] : "questionnaire"
  }
  let typeOptions = ["questionnaire", "risk_questionnaire"];

  let riskCalculationField = {
    "label": "Risk Calculation",
    "required" : true,
    "value": userAnswers.current["risk_calculation"] ? userAnswers.current["risk_calculation"] : "none"
  }
  let riskCalculationOptions = ["none", "zaita_approx", "highest_value"];

  function saveAnswersCallback() {
    SaveAnswers("admin.content.pillar.create", setSaveOk, setSaveErrors, userAnswers.current)
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
          {/* Caption */}
          <div className="w-full">
          <TextField field={captionField} value={captionField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="65px" camalCase runInit/>
          </div>
          {/* Key Information */}
          <div className="w-full">
          <TextAreaField field={keyInformationField} value={keyInformationField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" dbFormat runInit/>
          </div>
          {/* Type */}
          <div className="w-full">
            <DropdownField field={typeField} value={typeField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={typeOptions} camalCase runInit/>
          </div>           
          {/* Risk Calculation */}
          <div className="w-full">
            <DropdownField field={riskCalculationField} value={riskCalculationField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={riskCalculationOptions} dbFormat runInit/>
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
    ["Pillars", "admin.content.pillars"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
