import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import Admin_DropdownField from '@/Components/Admin/Inputs/Admin.DropdownField';
import Admin_RichTextAreaField from '@/Components/Admin/Inputs/Admin.RichTextAreaField';

export default function EditPillars(props) {  
  console.log("Admin.Content.Pillars.Edit");
  let [errors, setErrors] = useState("");
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

  function saveCallback() {
    userAnswers.current["id"] = props.pillar.id;    
    SaveAnswers("admin.content.pillar.save", setSaveOk, setErrors, userAnswers.current)
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true, runInit:true}

  function MyContent() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Edit Pillar</div>
        <div className="inline-block w-11/12">
          <Admin_TextField field={nameField} {...inputProps}/>
          <Admin_TextField field={captionField} {...inputProps}/>
          <Admin_RichTextAreaField field={keyInformationField} {...inputProps}/>
          <Admin_DropdownField field={typeField} options={typeOptions} {...inputProps}/>
          <Admin_DropdownField field={riskCalculationField} options={riskCalculationOptions} {...inputProps}/>
          <Admin_DropdownField field={approvalFlowField} options={props.approvalFlowOptions} {...inputProps}/>
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
        </div> 
      </div>
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
