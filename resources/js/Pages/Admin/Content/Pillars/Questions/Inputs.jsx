import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import InputList from '@/Components/Admin/Questionnaire/InputList';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';


export default function PillarEditQuestion(props) {  
  console.log("Admin.Content.Pillars.Questions.Inputs");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
  
  function saveAnswersCallback() {
    SaveAnswersWithId("admin.content.pillar.question.update", props.pillar.id, setSaveOk, setSaveErrors, userAnswers.current)
  }

  let MyContent= <InputList
    addRoute="admin.content.pillar.question.input.add"
    saveOrderRoute="admin.content.pillar.question.inputs.update"
    editRoute="admin.content.pillar.question.input.edit"
    deleteRoute="admin.content.pillar.question.input.delete"
    question={props.question}
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
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={MyContent}/>
  );
}
