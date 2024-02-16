import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function Task_Add(props) {  
  let nameField = { 
    "label": "Name",
    "type": "text",
    "required": true,
  }
  
  let typeField = {
    "label": "Type",
    "type": "dropdown",
    "required" : true,
    "options": ["questionnaire", "risk_questionnaire", "security_risk_assessment", "control_validation_audit"],
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(typeField);
  
  let myContent = <Admin_AddScreen {...props} inputFields={inputFields} 
    createRoute="admin.content.task.create"
    title="Add New Task"/>

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
