import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_ActionAddScreen from '@/Components/Admin/Admin.ActionAddScreen';

export default function Pillar_Question_Action_Add(props) {
  let myContent = <Admin_ActionAddScreen 
      createRoute="admin.content.pillar.question.action.create"
      createRouteParameters={{id:props.pillar.id, questionId:props.question.id}}
      isPillar
      {...props}
    />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", {id: props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", {id: props.pillar.id, questionId:props.question.id}],
    ["Add", "admin.content.pillar.question.action.add", {id: props.pillar.id, questionId:props.question.id}]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}