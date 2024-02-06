import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';

import { SaveAnswers } from '@/Components/Admin/SaveAnswers';
import RichTextAreaField from '@/Components/RichTextAreaField';

export default function EditPillars(props) {  
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
    "value": props.pillar ? props.pillar.name : ""
  }

  let captionField = { 
    "label": "Caption",
    "placeholder": "",
    "required": true,
    "value": props.pillar?.caption ? props.pillar.caption : ""
  }

  let keyInformationField = { 
    "label": "Key Information",
    "placeholder": "",
    "required": true,
    "value": props.pillar?.key_information ? props.pillar.key_information : ""
  }

  let approvalFlowField = {
    "label": "Approval Flow",
    "required" : true,
    "value": props.pillar?.approval_flow.name ? props.pillar.approval_flow.name : "undefined"
  }
  
  let typeField = {
    "label": "Type",
    "required" : true,
    "value": props.pillar?.questionnaire.type ? props.pillar.questionnaire.type : "questionnaire"
  }
  let typeOptions = ["questionnaire", "risk_questionnaire"];

  let riskCalculationField = {
    "label": "Risk Calculation",
    "required" : true,
    "value": props.pillar?.questionnaire.risk_calculation ? props.pillar.questionnaire.risk_calculation : "none"
  }
  let riskCalculationOptions = ["none", "zaita_approx", "highest_value"];

  function saveAnswersCallback() {
    userAnswers.current["id"] = props.pillar.id;    
    SaveAnswers("admin.content.pillar.save", setSaveOk, setSaveErrors, userAnswers.current)
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
            <RichTextAreaField field={keyInformationField} value={keyInformationField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" dbFormat runInit/>
          </div>
          {/* Approval Flow */}
          <div className="w-full">
            <DropdownField field={approvalFlowField} value={approvalFlowField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={props.approvalFlowOptions} dbFormat runInit/>
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
      <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
        <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/></div>
        <div className="pl-2 font-bold">{saveOk}</div>
      </div> 
      </>
    );
  }

  let topMenuItems = [
    ["Pillar", "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    ["Tasks", "admin.content.pillar.tasks", props.pillar.id],
  ]

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
