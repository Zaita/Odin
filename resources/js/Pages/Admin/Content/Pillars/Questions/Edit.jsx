import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionEdit from '@/Components/Admin/Questionnaire/Questions/Edit';

export default function PillarEditQuestion(props) {  
  console.log("Admin.Content.Pillars.Questions.Edit");  

  let x = <QuestionEdit
    question={props.question}
    saveRoute="admin.content.pillar.question.save"
    saveRouteParameters={{id:props.pillar.id, questionId:props.question.id}}
    {...props}
    />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
  ] 

  let topMenuItems = [
    ["Question", "admin.content.pillar.question.edit", { id:props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", { id:props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", { id:props.pillar.id, questionId:props.question.id}],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
