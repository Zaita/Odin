import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import InputList from '@/Components/Admin/Questionnaire/InputList';

export default function TaskQuestionInputsView(props) {  
  console.log("Admin.Content.Task.Questions.Inputs");  

  let MyContent= <InputList
    addRoute="admin.content.task.question.input.add"
    saveOrderRoute="admin.content.task.question.inputs.reorder"
    editRoute="admin.content.task.question.input.edit"
    deleteRoute="admin.content.task.question.input.delete"
    question={props.question}
    {...props}
  />

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
    [props.question.title, "admin.content.task.question.edit", { id:props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.actions", { id:props.task.id, questionId:props.question.id}],
  ]

  let topMenuItems = [
    ["Question", "admin.content.task.question.edit", { id:props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.inputs", { id:props.task.id, questionId:props.question.id}],
    ["Actions", "admin.content.task.question.actions", { id:props.task.id, questionId:props.question.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={MyContent}/>
  );
}
