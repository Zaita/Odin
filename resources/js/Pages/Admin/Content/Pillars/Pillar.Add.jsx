import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function EditPillars(props) {  
  let nameField = { 
    "label": "Name",
    "type": "text",
    "required": true,
  }

  let captionField = { 
    "label": "Caption",
    "type": "text",
    "required": true,
  }

  let keyInformationField = { 
    "label": "Key Information",
    "type": "richtextarea",
    "required": true,
  }

  let approvalFlowField = {
    "label": "Approval Flow",
    "type": "dropdown",
    "required" : true,
    "value": "",
    "options": props.approvalFlowOptions,
  }
  
  let typeField = {
    "label": "Type",
    "type": "dropdown",
    "required" : true,
    "options": ["questionnaire", "risk_questionnaire"]
  }

  let riskCalculationField = {
    "label": "Risk Calculation",
    "type": "dropdown",
    "required" : true,
    "value": "none",
    "options": ["none", "zaita_approx", "highest_value"],
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(captionField);
  inputFields.push(keyInformationField);
  inputFields.push(approvalFlowField);
  inputFields.push(typeField);
  inputFields.push(riskCalculationField);

  let myContent = <Admin_AddScreen {...props} inputFields={inputFields} 
    createRoute="admin.content.pillar.create"
    title="Add New Pillar"/>

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
