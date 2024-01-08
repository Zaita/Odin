import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import QuestionsList from '@/Components/Admin/QuestionsList';

export default function TaskQuestions(props) {  
  let [saveErrors, setSaveErrors] = useState(props.errors ? props.errors : null);
  let [saveOk, setSaveOk] = useState(null);

  let x = <QuestionsList
    addRoute="admin.content.task.question.add"
    saveOrderRoute="admin.content.pillar.task.update"
    editRoute="admin.content.pillar.task.edit"
    deleteRoute="admin.content.pillar.task.delete"
    questions={props.task.questionnaire.questions}
    {...props}
    />

  /**
   * Handle building our Top Menu Items and Action Buttons
   */
  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
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
