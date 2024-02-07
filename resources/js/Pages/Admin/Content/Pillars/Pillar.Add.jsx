import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import Admin_DropdownField from '@/Components/Admin/Inputs/Admin.DropdownField';
import Admin_RichTextAreaField from '@/Components/Admin/Inputs/Admin.RichTextAreaField';

export default function Content_Pillars_Add(props) {  
  console.log("Admin.Content.Pillars.Add");  
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
    "value": userAnswers.current["type"]
  }
  let typeOptions = ["questionnaire", "risk_questionnaire"];

  let riskCalculationField = {
    "label": "Risk Calculation",
    "required" : true,
    "value": userAnswers.current["risk_calculation"]
  }
  let riskCalculationOptions = ["none", "zaita_approx", "highest_value"];

  let approvalFlowField = {
    "label": "Approval Flow",
    "required" : true,
    "value": userAnswers.current["approval_flow"]
  }

  function saveCallback() {
    SaveAnswers("admin.content.pillar.create", setSaveOk, setErrors, userAnswers.current)
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

  function MyContent() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Add New Pillar</div>
        <div className="inline-block w-11/12">
          <Admin_TextField field={nameField} {...inputProps}/>
          <Admin_TextField field={captionField} {...inputProps}/>
          <Admin_RichTextAreaField field={keyInformationField} {...inputProps}/>
          <Admin_DropdownField field={typeField} options={typeOptions} {...inputProps} runInit/>
          <Admin_DropdownField field={riskCalculationField} options={riskCalculationOptions} {...inputProps} runInit/>
          <Admin_DropdownField field={approvalFlowField} options={props.approvalFlowOptions} {...inputProps} runInit/>
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
        </div> 
      </div>
    );
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
