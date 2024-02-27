import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function Task_Questionnaire_Edit(props) {  
  console.log("Admin.Content.Task(Questionnaire).Edit");

  let nameField = { 
    "label" : "Name",
    "type": "text",
    "placeholder": "",
    "required": true,
    "value": props.task ? props.task.name : ""
  }

  let typeField = {
    "label": "Type",
    "type": "dropdown",
    "required" : true,
    "value": props.task?.type ? props.task.type : "questionnaire",
    "options": ["questionnaire", "risk_questionnaire"]
  }
  
  let keyInformationField = { 
    "label": "Key Information",
    "type": "richtextarea",
    "placeholder": "",
    "required": false,
    "value": props.task?.key_information ? props.task.key_information : ""
  }

  let lockWhenCompleteField = { 
    "label": "Lock When Complete",
    "type": "checkbox",
    "required": false,
    "value": props.task?.lock_when_complete ? props.task.lock_when_complete : false,
    "visibility" : true
  }

  let approvalRequiredField = { 
    "label": "Approval Required",
    "type": "checkbox",
    "required": false,
    "value": props.task?.approval_required ? props.task.approval_required : false,
    "visibility" : true
  }

  let groupNames = [];
  groupNames.push("none");
  props.groups.map((group) => groupNames.push(group.name));
  
  let approvalGroupField = { 
    "label": "Approval Group",
    "type": "dropdown",    
    "required": false,
    "value": props.task?.approval_group ? props.task.approval_group : "none",
    "options" : groupNames
  }

  let notificationGroupField = { 
    "label": "Notification Group",
    "type": "dropdown",
    "required": false,
    "value": props.task?.notification_group ? props.task.notification_group : "none",
    "options" : groupNames
  }

  let riskCalculationField = {
    "label": "Risk Calculation",
    "type": "dropdown",
    "required" : true,
    "value": props.questionnaire.risk_calculation ? props.questionnaire.risk_calculation : "none",
    "options" : ["none", "zaita_approx", "highest_value"],
  }

  let customRisksField = {
    "label": "Custom Risks",
    "type": "checkbox",
    "required" : true,
    "value": props.questionnaire.custom_risks ? props.questionnaire.custom_risks : false,
  }

  let timeToCompleteField = {
    "label": "Time to Complete",
    "type": "text",
    "required" : false,
    "value": props.questionnaire.time_to_complete ? props.questionnaire.time_to_complete : "",
  }

  let timeToReviewField = {
    "label": "Time to review",
    "type": "text",
    "required" : false,
    "value": props.questionnaire.time_to_review ? props.questionnaire.time_to_review : "",
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(typeField);
  inputFields.push(keyInformationField);
  inputFields.push(lockWhenCompleteField);
  inputFields.push(approvalRequiredField);
  inputFields.push(approvalGroupField);
  inputFields.push(notificationGroupField);
  inputFields.push(riskCalculationField);
  inputFields.push(customRisksField);
  inputFields.push(timeToCompleteField);
  inputFields.push(timeToReviewField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.content.task.save"
    saveRouteParameters={props.task.id}
    title="Modify Questionnaire/RiskQuestionnaire Task"/>

  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ]

  if (props.questionnaire.custom_risks) {
    topMenuItems.push(["Risks", "admin.content.task.risks", props.task.id])
  }

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
