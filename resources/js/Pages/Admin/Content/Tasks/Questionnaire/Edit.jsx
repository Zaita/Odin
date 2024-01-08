import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';
import CheckBoxField from '@/Components/CheckBoxField';

import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function TaskQuestionnaireEdit(props) {  
  console.log("Admin.Content.Task(Questionnaire).Edit");
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    console.log(`task(questionnaire).edit[${id}] = ${value}`);
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "",
    "required": true,
    "value": props.task ? props.task.name : ""
  }

  let typeField = {
    "label": "Type",
    "required" : true,
    "value": props.task?.type ? props.task.type : "questionnaire"
  }
  let typeOptions = ["questionnaire", "risk_questionnaire"];

  let keyInformationField = { 
    "label": "Key Information",
    "placeholder": "",
    "required": false,
    "value": props.task?.key_information ? props.task.key_information : ""
  }

  let lockWhenCompleteField = { 
    "label": "Lock When Complete",
    "required": false,
    "value": props.task?.lock_when_complete ? props.task.lock_when_complete : false,
    "visibility" : true
  }

  let approvalRequiredField = { 
    "label": "Approval Required",
    "required": false,
    "value": props.task?.approval_required ? props.task.approval_required : false,
    "visibility" : true
  }

  let approvalGroupField = { 
    "label": "Approval Group",
    "required": false,
    "value": props.task?.approval_group ? props.task.approval_group : ""
  }
  let groupNames = [];
  props.groups.map((group) => groupNames.push(group.name));

  let notificationGroupField = { 
    "label": "Notification Group Group",
    "required": false,
    "value": props.task?.notification_group ? props.task.notification_group : ""
  }

  let riskCalculationField = {
    "label": "Risk Calculation",
    "required" : true,
    "value": props.task?.risk_calculation ? props.task.risk_calculation : "none"
  }
  let riskCalculationOptions = ["none", "zaita_approx", "highest_value"];

  function saveAnswersCallback() {
    userAnswers.current["id"] = props.task.id;    
    SaveAnswers("admin.content.task.save", setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Name */}
          <div className="w-full">
          <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>
          {/* Type */}
          <div className="w-full">
            <DropdownField field={typeField} value={typeField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={typeOptions} camalCase runInit/>
          </div>                        
          {/* Key Information */}
          <div className="w-full">
            <TextAreaField field={keyInformationField} value={keyInformationField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="200px" dbFormat runInit/>
          </div>        
          {/* Risk Calculation */}
          <div className="w-full">
            <DropdownField field={riskCalculationField} value={riskCalculationField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={riskCalculationOptions} dbFormat runInit/>
          </div>   
          {/* Lock When Complete */}
          <div>
            <CheckBoxField field={lockWhenCompleteField} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit sideBySide/>
          </div>     
          {/* Approval Required */}
          <div>
            <CheckBoxField field={approvalRequiredField} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat runInit sideBySide/>
          </div>   
          {/* Approval Group Name */}
          <div className="w-full">
            <DropdownField field={approvalGroupField} value={approvalGroupField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={groupNames} dbFormat runInit/>
          </div>    
          {/* Notification Group Name */}
          <div className="w-full">
            <DropdownField field={notificationGroupField} value={notificationGroupField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={groupNames} dbFormat runInit/>
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

  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ]

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
