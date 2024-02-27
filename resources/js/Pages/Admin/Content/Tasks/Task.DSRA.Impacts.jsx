import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function Task_DSRA_Edit(props) {  
  console.log("Admin.Content.Task(DSRA).Edit");

  let nameField = { 
    "label" : "Name",
    "type": "text",
    "placeholder": "",
    "required": true,
    "value": props.task ? props.task.name : ""
  }
  
  let keyInformationField = { 
    "label": "Key Information",
    "type": "richtextarea",
    "placeholder": "",
    "required": false,
    "value": props.task?.key_information ? props.task.key_information : ""
  }

  let customLikelihoodField = {
    "label": "Custom Likelihoods",
    "type": "checkbox",
    "required" : true,
    "value": props.questionnaire.custom_likelihoods ? props.questionnaire.custom_likelihoods : false,
  }

  let customImpactField = {
    "label": "Custom Impacts",
    "type": "checkbox",
    "required" : true,
    "value": props.questionnaire.custom_impacts ? props.questionnaire.custom_impacts : false,
  }

  let timeToCompleteField = {
    "label": "Time to Complete",
    "type": "text",
    "required" : false,
    "value": props.questionnaire.time_to_complete ? props.questionnaire.time_to_complete : false,
  }

  let timeToReviewField = {
    "label": "Time to review",
    "type": "text",
    "required" : false,
    "value": props.questionnaire.time_to_review ? props.questionnaire.time_to_review : false,
  }


  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(typeField);
  inputFields.push(keyInformationField);
  inputFields.push(customLikelihoodField);
  inputFields.push(customImpactField);
  inputFields.push(timeToCompleteField);
  inputFields.push(timeToReviewField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.content.task.save"
    saveRouteParameters={props.task.id}
    title="Modify Digital Security Risk Assessment Task"/>

  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
  ]

  if (props.dsra.custom_likelihoods) {
    topMenuItems.push(["Likelihoods", "admin.content.task.likelihoods", props.task.id])
  }

  if (props.dsra.custom_impacts) {
    topMenuItems.push(["Impacts", "admin.content.task.impacts", props.task.id])
  }

  topMenuItems.push(["Risk Matrix", "admin.content.task.riskmatrix", props.task.id])

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
