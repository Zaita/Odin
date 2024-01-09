import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionEdit from '@/Components/Admin/Questionnaire/Questions/Edit';

export default function TaskEditQuestion(props) {  
  console.log("Admin.Content.Task.Questions.Edit");  

  let x = <QuestionEdit
    question={props.question}
    saveRoute="admin.content.task.question.save"
    saveRouteParameters={{id:props.task.id, questionId:props.question.id}}
    {...props}
    />

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
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
