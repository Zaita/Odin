import React, { useRef, useState, Component } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionsList from '@/Components/Admin/QuestionsList';

export default function Task_Questionnaire_Questions(props) {  
  let x = <QuestionsList
    objectId={props.task.id}
    addRoute="admin.content.task.question.add"
    saveOrderRoute="admin.content.task.questions.reorder"
    saveOrderParameters={props.task.id}
    editRoute="admin.content.task.question.edit"
    deleteRoute="admin.content.task.question.delete"
    questions={props.task.questionnaire.questions}
    {...props}
    />

  /**
   * Handle building our Top Menu Items and Action Buttons
   */
  let topMenuItems = [
    ["Tasks", "admin.content.tasks"],
    // [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ]

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
