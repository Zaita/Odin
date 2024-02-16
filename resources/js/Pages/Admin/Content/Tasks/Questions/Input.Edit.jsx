
import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_InputEditScreen from '@/Components/Admin/Inputs/Admin.InputEditScreen';

export default function Task_Question_Input_Edit(props) {
  let myContent = <Admin_InputEditScreen
    saveRoute="admin.content.task.question.input.save"    
    addCheckboxRoute="admin.content.task.question.input.checkbox.add"
    editCheckboxRoute="admin.content.task.question.input.checkbox.edit"
    deleteCheckboxRoute="admin.content.task.question.input.checkbox.delete"
    routeParameters={{id:props.task.id, questionId:props.question.id, inputId:props.input.id}}
    objectId={props.task.id}
    isTask
    {...props}
  />

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
    [props.question.title, "admin.content.task.question.edit", {id: props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.inputs", {id: props.task.id, questionId:props.question.id}],
    [props.input.label, "admin.content.task.question.input.edit", {id: props.task.id, questionId:props.question.id, inputId:props.input.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}