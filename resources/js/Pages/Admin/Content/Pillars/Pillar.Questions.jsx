import React, { useRef, useState, Component } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionsList from '@/Components/Admin/QuestionsList';

export default function Pillar_Questions(props) {  
  let x = <QuestionsList
    objectId={props.pillar.id}
    addRoute="admin.content.pillar.question.add"
    saveOrderRoute="admin.content.pillar.questions.reorder"
    saveOrderParameters={props.pillar.id}
    editRoute="admin.content.pillar.question.edit"
    deleteRoute="admin.content.pillar.question.delete"
    questions={props.pillar.questionnaire.questions}
    {...props}
  />

  /**
   * Handle building our Top Menu Items and Action Buttons
   */
  let topMenuItems = [
    ["Pillar", "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    ["Tasks", "admin.content.pillar.tasks", props.pillar.id],
  ]
  
  if (props.pillar?.questionnaire.custom_risks) {
    topMenuItems.push(["Risks", "admin.content.pillar.risks", props.pillar.id])
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
