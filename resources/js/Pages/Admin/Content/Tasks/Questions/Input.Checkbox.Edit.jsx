import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import InputCheckBoxAddEdit from '@/Components/Admin/Questionnaire/InputFields/Checkbox/AddEdit'; 

export default function Task_Question_Input_Checkbox_Edit(props) {
 
  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
    [props.question.title, "admin.content.task.question.edit", {id: props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.inputs", {id: props.task.id, questionId:props.question.id}],
    [props.input.label, "admin.content.task.question.input.edit", {id: props.task.id, questionId:props.question.id, inputId:props.input.id}],
    [props.option.label, "admin.content.task.question.input.checkbox.edit", {id:props.task.id, questionId:props.question.id, inputId:props.input.id, optionId:props.option.id}],
  ]

  let EditScreen = <InputCheckBoxAddEdit 
      saveRoute="admin.content.task.question.input.checkbox.save"      
      addCheckboxRoute="admin.content.task.question.input.checkbox.add"
      editCheckboxRoute="admin.content.task.question.input.checkbox.edit"
      deleteCheckboxRoute="admin.content.task.question.input.checkbox.delete"
      routeParameters={{id:props.task.id, questionId:props.question.id, inputId:props.input.id, optionId:props.option.id}}
      {...props}/>; 

  return (
    <AdminPanel {...props} breadcrumb={breadcrumb} content={EditScreen}/>
  );
}