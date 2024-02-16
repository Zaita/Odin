import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function Task_Question_Edit(props) {  
  let titleField = { 
    "label" : "Title",
    "type" : "textfield",
    "required": true,
    "value": props.question.title,
  }

  let headingField = { 
    "label": "Heading",
    "type": "text",
    "required": true,
    "value": props.question.heading,
  }

  let descriptionField = { 
    "label" : "Description",
    "type" : "richtextarea",
    "required": false,
    "value": props.question.description,
  }

  let inputFields = [];
  inputFields.push(titleField);
  inputFields.push(headingField);
  inputFields.push(descriptionField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
  saveRoute="admin.content.pillar.question.save"
  saveRouteParameters={{id:props.pillar.id, questionId:props.question.id}}
  title="Modify Existing Question"/>

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
  ] 

  let topMenuItems = [
    ["Question", "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", { id:props.pillar.id, questionId:props.question.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
