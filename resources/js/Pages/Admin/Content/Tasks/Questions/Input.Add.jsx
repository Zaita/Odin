import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_InputAddScreen from '@/Components/Admin/Inputs/Admin.InputAddScreen';

export default function Task_Question_Input_Add(props) {
  
  let myContent = <Admin_InputAddScreen 
      createRoute="admin.content.task.question.input.create"
      createRouteParameters={{id:props.task.id, questionId:props.question.id}}
      isTask
      {...props}
    />

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
    [props.task.name, "admin.content.task.edit", props.task.id],
    ["Questions", "admin.content.task.questions", props.task.id],
    [props.question.title, "admin.content.task.question.edit", {id: props.task.id, questionId:props.question.id}],
    ["Inputs", "admin.content.task.question.inputs", {id: props.task.id, questionId:props.question.id}],
    ["Add", "admin.content.task.question.input.add", {id: props.task.id, questionId:props.question.id}]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}