import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function Task_Question_Add(props) {  
  let titleField = { 
    "label" : "Title",
    "type" : "textfield",
    "required": true,
  }

  let headingField = { 
    "label": "Heading",
    "type": "text",
    "required": true,
  }

  let descriptionField = { 
    "label": "Description",
    "type": "richtextarea",
    "required": false,
  }

  let inputFields = [];
  inputFields.push(titleField);
  inputFields.push(headingField);
  inputFields.push(descriptionField);
  
  let myContent = <Admin_AddScreen {...props} inputFields={inputFields} 
    createRoute="admin.content.task.question.create"
    createRouteParameters={{id:props.task.id}}
    title="Add New Question"/>

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ] 

  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
