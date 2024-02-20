import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_ActionEditScreen from '@/Components/Admin/Admin.ActionEditScreen';

export default function Pillar_Question_Action_Edit(props) {
  let myContent = <Admin_ActionEditScreen 
      saveRoute="admin.content.pillar.question.action.save"
      saveRouteParameters={{id:props.pillar.id, questionId:props.question.id, actionId:props.action.id}}
      isPillar
      {...props}
    />

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    [props.question.title, "admin.content.pillar.question.edit", {id: props.pillar.id, questionId:props.question.id}],
    ["Actions", "admin.content.pillar.question.actions", {id: props.pillar.id, questionId:props.question.id}],
    [props.action.label, "admin.content.pillar.question.action.edit", {id: props.pillar.id, questionId:props.question.id, actionId:props.action.id}]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}