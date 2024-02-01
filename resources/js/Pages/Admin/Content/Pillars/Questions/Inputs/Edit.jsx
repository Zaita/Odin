
import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import EditInputField from '@/Components/Admin/Questionnaire/InputFields/Edit';

export default function PillarQuestionsInputsEdit(props) {
 
  let topMenuItems = [];

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}]
    // [props.input.label, "admin.content.pillar.question.input.edit", { id:props.pillar.id, questionId:props.question.id, inputId:props.input.id}]
  ]

  let EditScreen = <EditInputField 
      saveRoute="admin.content.pillar.question.input.save"      
      addCheckboxRoute="admin.content.pillar.question.input.checkbox.add"
      editCheckboxRoute="admin.content.pillar.question.input.checkbox.edit"
      deleteCheckboxRoute="admin.content.pillar.question.input.checkbox.edit"
      routeParameters={{id:props.pillar.id, questionId:props.question.id, inputId:props.field.id}}
      {...props}/>; 

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={EditScreen}/>
  );
}