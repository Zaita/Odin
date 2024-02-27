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
    "value": props.dsra?.key_information ? props.dsra.key_information : ""
  }

  let initialRiskOptions = []
  props.riskQuestionnaires?.map((rq) => initialRiskOptions.push(rq.name));
  let initialImpactField = {
    "label": "Initial Risk Impact",
    "type": "dropdown",
    "required" : true,
    "value": props.dsra.initial_risk_impact ? props.dsra.initial_risk_impact.name : "",
    "options" : initialRiskOptions,
  }

  let securityCatalogueOptions = []
  props.securityCatalogues.map((cat) => securityCatalogueOptions.push(cat.name));
  let securityCatalogueField = {
    "label": "Security Catalogue",
    "type": "dropdown",
    "required" : true,
    "value": props.dsra.security_catalogue ? props.dsra.security_catalogue.name : "",
    "options" : securityCatalogueOptions,
  }

  let customLikelihoodField = {
    "label": "Custom Likelihoods",
    "type": "checkbox",
    "required" : false,
    "value": props.dsra.custom_likelihoods ? props.dsra.custom_likelihoods : false,
  }

  let customImpactField = {
    "label": "Custom Impacts",
    "type": "checkbox",
    "required" : false,
    "value": props.dsra.custom_impacts ? props.dsra.custom_impacts : false,
  }

  let timeToCompleteField = {
    "label": "Time to Complete",
    "type": "text",
    "required" : false,
    "value": props.dsra.time_to_complete ? props.dsra.time_to_complete : "",
  }

  let timeToReviewField = {
    "label": "Time to review",
    "type": "text",
    "required" : false,
    "value": props.dsra.time_to_review ? props.dsra.time_to_review : "",
  }


  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(keyInformationField);
  inputFields.push(initialImpactField);
  inputFields.push(securityCatalogueField);
  inputFields.push(customLikelihoodField);
  inputFields.push(customImpactField);
  inputFields.push(timeToCompleteField);
  inputFields.push(timeToReviewField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.content.task.dsrasave"
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
