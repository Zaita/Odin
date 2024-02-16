import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function EditPillars(props) {  
  let nameField = { 
    "label": "Name",
    "type": "text",
    "required": true,
    "value": props.pillar ? props.pillar.name : ""
  }

  let captionField = { 
    "label": "Caption",
    "type": "text",
    "required": true,
    "value": props.pillar?.caption ? props.pillar.caption : ""
  }

  let keyInformationField = { 
    "label": "Key Information",
    "type": "richtextarea",
    "required": true,
    "value": props.pillar?.key_information ? props.pillar.key_information : ""
  }

  let approvalFlowField = {
    "label": "Approval Flow",
    "type": "dropdown",
    "required" : true,
    "value": props.pillar?.approval_flow.name ? props.pillar.approval_flow.name : "undefined",
    "options": props.approvalFlowOptions,
  }
  
  let typeField = {
    "label": "Type",
    "type": "dropdown",
    "required" : true,
    "value": props.pillar?.questionnaire.type ? props.pillar.questionnaire.type : "questionnaire",
    "options": ["questionnaire", "risk_questionnaire"]
  }

  let riskCalculationField = {
    "label": "Risk Calculation",
    "type": "dropdown",
    "required" : true,
    "value": props.pillar?.questionnaire.risk_calculation ? props.pillar.questionnaire.risk_calculation : "none",
    "options": ["none", "zaita_approx", "highest_value"],
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(captionField);
  inputFields.push(keyInformationField);
  inputFields.push(approvalFlowField);
  inputFields.push(typeField);
  inputFields.push(riskCalculationField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.content.pillar.save"
    saveRouteParameters={props.pillar.id}
    title="Modify Pillar"/>

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
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
