import React, { useRef, useState } from 'react';

import ThemedButton from "@/Components/ThemedButton";
import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";
import Admin_TextField from './Inputs/Admin.TextField';
import Admin_DropdownField from './Inputs/Admin.DropdownField';
import Admin_MultiSelectField from './Inputs/Admin.MultiSelectField';

export default function Admin_ActionAddScreen(props) {
  let [errors, setErrors] = useState();
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [renderFlag, setRenderFlag] = useState("");
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function handleChange(id, value) {
    userAnswers.current[id] = value;
    if (id == "action_type") {
      setRenderFlag(value);   
    }
  }

  function saveCallback() {
    SaveAnswersWithId(props.createRoute, props.createRouteParameters, setSaveOk, setErrors, userAnswers.current);
  }

  let labelField = { 
    "label" : "Label",
    "type": "text",
    "required": true,
  }

  let actionTypeField = {
    "label" : "Action Type",
    "type": "dropdown",
    "required": true,
    "options": ["", "continue", "goto", "finish", "message"]
  }

  let tasksField = {
    "label" : "Tasks",
    "type": "dropdown",
    "required": true,
    "options": props.taskNames
  }

  let gotoQuestionTitleField = {
    "label" : "Goto Question",
    "type": "dropdown",
    "required": true,
    "options": props.questionNames
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

  return (
    <div className="pt-1 pb-2">
      <div className="font-bold pb-2">Add New Action Field</div>
        <div className="inline-block w-1/2"> 
          <div className="w-full">
            <Admin_TextField field={labelField} {...inputProps}/>
          </div>
          <div className="w-full">
            <Admin_DropdownField field={actionTypeField} {...inputProps} />
          </div>  
          <div className="w-full">
            <Admin_MultiSelectField field={tasksField} {...inputProps} />
          </div>            
          {renderFlag == "goto" && <div className="w-full">
            <Admin_DropdownField field={gotoQuestionTitleField} {...inputProps}/>
          </div>}                     
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
          <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
        </div>  
    </div>
  )
}