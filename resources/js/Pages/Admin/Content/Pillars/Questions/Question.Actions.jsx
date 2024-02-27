import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import ActionList from '@/Components/Admin/Questionnaire/ActionList';

export default function InputsView(props) {  
  console.log("Admin.Content.Pillars.Questions.Inputs");  

  let MyContent= <ActionList
    addRoute="admin.content.pillar.question.action.add"
    saveOrderRoute="admin.content.pillar.question.actions.reorder"
    editRoute="admin.content.pillar.question.action.edit"
    deleteRoute="admin.content.pillar.question.action.delete"
    question={props.question}
    {...props}
  />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", { id:props.pillar.id, questionId:props.question.id}],
  ]

  let topMenuItems = [
    ["Question", "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", { id:props.pillar.id, questionId:props.question.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={MyContent}/>
  );
}
