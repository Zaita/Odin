import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_InputAddScreen from '@/Components/Admin/Inputs/Admin.InputAddScreen';

export default function Pillar_Question_Input_Add(props) {
  let myContent = <Admin_InputAddScreen 
      createRoute="admin.content.pillar.question.input.create"
      createRouteParameters={{id:props.pillar.id, questionId:props.question.id}}
      isTask
      {...props}
    />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", {id: props.pillar.id, questionId:props.question.id}],
    ["Inputs", "admin.content.pillar.question.inputs", {id: props.pillar.id, questionId:props.question.id}],
    ["Add", "admin.content.pillar.question.input.add", {id: props.pillar.id, questionId:props.question.id}]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}