import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function Submission_View(props) {  
  let uuidField = { 
    "label" : "UUID (Unique Id)",
    "type": "textreadonly",
    "required": false,
    "value": props.submission.uuid
  }

  let pillarNameField = { 
    "label" : "Pillar Name",
    "type": "textreadonly",
    "required": false,
    "value": props.submission.pillar_name
  }

  let typeField = { 
    "label" : "Pillar Type",
    "type": "textreadonly",
    "required": false,
    "value": props.submission.type
  }

  let riskCalculationField = { 
    "label" : "Risk Calculation",
    "type": "textreadonly",
    "required": false,
    "value": props.submission.risk_calculation
  }

  let productNameField = { 
    "label" : "Product Name",
    "type": "text",
    "required": false,
    "value": props.submission.product_name
  }

  let inputFields = [];
  inputFields.push(uuidField);
  inputFields.push(pillarNameField);
  inputFields.push(typeField);
  inputFields.push(riskCalculationField);
  inputFields.push(productNameField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.records.submission.save"
    saveRouteParameters={props.submission.id}
    title={"Submission: " + props.submission.uuid}/>

  let topMenuItems = [
    ["Main", "admin.records.submission.view", props.submission.id],
  ]

  let breadcrumb = [
    ["Submissions", "admin.records.submissions"],
    [props.submission.uuid, "admin.records.submission.view", props.submission.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
