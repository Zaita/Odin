import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import QuestionAdd from '@/Components/Admin/Questionnaire/Questions/Add';

export default function PillarAddQuestion(props) {  
  let x = <QuestionAdd
  saveRoute="admin.content.pillar.question.create"
  saveRouteParameters={{id:props.pillar.id}}
  {...props}
  />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={x}/>
  );
}
