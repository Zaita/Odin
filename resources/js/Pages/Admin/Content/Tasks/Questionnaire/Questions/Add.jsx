import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionAdd from '@/Components/Admin/Questionnaire/Questions/Add';

export default function TaskAddQuestion(props) {  
  let x = <QuestionAdd
  saveRoute="admin.content.task.question.create"
  saveRouteParameters={{id:props.task.id}}
  {...props}
  />

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ] 

  let topMenuItems = [
    ["Task", "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
