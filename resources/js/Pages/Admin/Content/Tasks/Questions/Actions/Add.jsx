
import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from "@/Components/TextField";
import DropdownField from "@/Components/DropdownField";
import ThemedButton from "@/Components/ThemedButton";
import MultiSelectField from '@/Components/MultiSelectField';
import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";

export default function ActionAdd(props) {
  console.log("Pillar.Question.Action.Add");
  let [saveErrors, setSaveErrors] = useState(props.errors);
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  function saveAnswersCallback() {
    userAnswers.current["pillarId"] = props.pillar.id;
    userAnswers.current["questionId"] = props.question.id;
    SaveAnswersWithId("admin.content.pillar.question.action.create", {id:props.pillar.id, questionId:props.question.id}, setSaveOk, setSaveErrors, userAnswers.current);
  }

  function getValue(targetProp, label, defaultValue="") {
    if (userAnswers.current[label] == null || userAnswers.current[label] == "") 
      userAnswers.current[label] = targetProp;
    return userAnswers.current[label] ? userAnswers.current[label] : (targetProp ?  targetProp : defaultValue);
  }

  let labelField = { 
    "label" : "Label",
    "placeholder": "",
    "required": true,
    "value": getValue(props.field?.label, "label")
  }

  let actionTypeField = {
    "label" : "Action Type",
    "required": true,
    "value": getValue(props.field?.input_type, "action_type", "continue")
  }
  let actionTypeOptions = [ "continue", "goto", "finish", "message"];

  let gotoQuestionTitle = {
    "label" : "Goto Question Title",
    "value" : getValue(props.field?.goto_question_title, "goto_question_title", false),
  }

  let taskField = {
    "label" : "Tasks",
    "value" : getValue(props.field?.tasks, "tasks", false),
  }
  
  const taskOptions = [ 
    "Privacy Threshold Assessment",
    "Information Data and Management Assessment",
    "Penetration Test",
    "Security Risk Assessment"];

  function MyContent() {
    return (
      <div>      
        <div id="question_field" className="w-full">
          <div className="inline-block w-full"> 
            {/* Label */}
            <div className="w-full">
              <TextField field={labelField} value={labelField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide runInit/>
            </div>
            {/* Action Type */}
            <div className="w-full">
              <DropdownField field={actionTypeField} value={actionTypeField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={actionTypeOptions} dbFormat sideBySide runInit/>
            </div>  
            {/* Goto Question */}
            <div>
            <DropdownField field={gotoQuestionTitle} value={gotoQuestionTitle.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={props.questionTitles} dbFormat sideBySide />
            </div>    
            {/* Tasks */} 
            <div>
            <MultiSelectField field={taskField} value={taskField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={taskOptions} dbFormat sideBySide />
            </div>                                       
          </div>          
          <div id="bottom_menu" className="h-10 pt-2">
          <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save New Input Field"/>
          <p>{saveOk}</p>
        </div>  
        </div>     
      </div>
    )
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}