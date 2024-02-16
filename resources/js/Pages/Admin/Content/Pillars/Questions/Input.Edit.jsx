import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_InputEditScreen from '@/Components/Admin/Inputs/Admin.InputEditScreen';

export default function Pillar_Question_Input_Edit(props) {
  let myContent = <Admin_InputEditScreen
    saveRoute="admin.content.pillar.question.input.save"    
    addCheckboxRoute="admin.content.pillar.question.input.checkbox.add"
    editCheckboxRoute="admin.content.pillar.question.input.checkbox.edit"
    deleteCheckboxRoute="admin.content.pillar.question.input.checkbox.delete"
    routeParameters={{id:props.pillar.id, questionId:props.question.id, inputId:props.input.id}}
    objectId={props.pillar.id}
    isPillar
    {...props}
  />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}],
    [props.input.label, "admin.content.pillar.question.input.edit", { id:props.pillar.id, questionId:props.question.id, inputId:props.input.id}]
  ]

  return (
  <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
