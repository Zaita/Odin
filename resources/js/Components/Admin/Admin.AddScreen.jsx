import React, { useRef, useState } from 'react';
import ReportIcon from '@mui/icons-material/Report';

import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import dbFormat from '@/Utilities/dbFormat';
import Admin_DropdownField from './Inputs/Admin.DropdownField';
import Admin_TextAreaField from './Inputs/Admin.TextAreaField';
import Admin_RichTextAreaField from './Inputs/Admin.RichTextAreaField';
import Admin_CheckBox from './Inputs/Admin.Checkbox';

export default function Admin_AddScreen(props) {  
  let [errors, setErrors] = useState();
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
 
  function saveCallback() {
    if (props.createRouteParameters) {
      SaveAnswersWithId(props.createRoute, props.createRouteParameters, setSaveOk, setErrors, userAnswers.current);
    } else {
      SaveAnswers(props.createRoute, setSaveOk, setErrors, userAnswers.current);
    }
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

  let inputFields = [];
  props.inputFields.map((field, key) => {
    field.value = userAnswers.current[dbFormat(field.label)] ? userAnswers.current[dbFormat(field.label)] : field.value;
    if (field.type == "textfield" || field.type == "text") {
      inputFields.push(<Admin_TextField key={key} field={field} {...inputProps}/>)
    } else if (field.type == "textarea") {
      inputFields.push(<Admin_TextAreaField key={key} field={field} {...inputProps}/>)
    } else if (field.type == "richtextarea") {
      inputFields.push(<Admin_RichTextAreaField key={key} field={field} {...inputProps}/>)
    } else if (field.type == "dropdown") {
      inputFields.push(<Admin_DropdownField key={key} field={field} options={field.options} {...inputProps} runInit/>)
    } else if (field.type == "checkbox") {
      inputFields.push(<Admin_CheckBox key={key} field={field} {...inputProps} runInit/>)
    }
  });   

  return (
    <div className="pt-1 pb-2">
      <div className="font-bold">{props.title}</div>
      <div className="inline-block w-11/12">
      {inputFields.map((field, index) => <span key={"ul_"+ index}>{field}</span>)}    
      </div>
      <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
        <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
        <div className="pl-2 font-bold">{saveOk}</div>
        <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
      </div> 
    </div>
  );
}
