
import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import InputCheckBoxAddEdit from '@/Components/Admin/Questionnaire/InputFields/Checkbox/AddEdit'; 

export default function PillarQuestionsInputsCheckboxAddEdit(props) {
 
  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}],
    [props.field.label, "admin.content.pillar.question.input.edit", { id:props.pillar.id, questionId:props.question.id, inputId:props.field.id}]
  ] 

  let EditScreen = <InputCheckBoxAddEdit 
      saveRoute="admin.content.pillar.question.input.checkbox.save"      
      addCheckboxRoute="admin.content.pillar.question.input.checkbox.add"
      editCheckboxRoute="admin.content.pillar.question.input.checkbox.edit"
      deleteCheckboxRoute="admin.content.pillar.question.input.checkbox.delete"
      routeParameters={{id:props.pillar.id, questionId:props.question.id, inputId:props.field.id, optionId:props.option.id}}
      {...props}/>; 

  return (
    <AdminPanel {...props} breadcrumb={breadcrumb} content={EditScreen}/>
  );
}