import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function Pillar_Question_Add(props) {  
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
    createRoute="admin.content.pillar.question.create"
    createRouteParameters={{id:props.pillar.id}}
    title="Add New Question"/>

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
