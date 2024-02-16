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
  saveRoute="admin.content.task.question.save"
  saveRouteParameters={{id:props.task.id, questionId:props.question.id}}
  title="Modify Existing Question"/>

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
    [props.question.title, "admin.content.task.question.edit", { id:props.task.id, questionId:props.question.id}],
  ] 

  let topMenuItems = [
    ["Question", "admin.content.task.question.edit", { id:props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.inputs", { id:props.task.id, questionId:props.question.id}],
    ["Actions", "admin.content.task.question.actions", { id:props.task.id, questionId:props.question.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
