import React, { useRef, useState } from 'react';
import ReportIcon from '@mui/icons-material/Report';

import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import dbFormat from '@/Utilities/dbFormat';

export default function Admin_EditScreen(props) {  
  let [errors, setErrors] = useState();
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
 
  function saveCallback() {
    SaveAnswersWithId(props.saveRoute, props.saveRouteParameters, setSaveOk, setErrors, userAnswers.current)
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true, runInit:true}

  let inputFields = [];
  props.inputFields.map((field, key) => {
    field.value = userAnswers.current[dbFormat(field.label)] ? userAnswers.current[dbFormat(field.label)] : field.value;
    if (field.type == "textfield") {
      inputFields.push(<Admin_TextField key={key} field={field} {...inputProps}/>)
    }
  });    

  return (
    <div className="pt-1 pb-2">
      <div className="font-bold">{props.title}</div>
      <div className="inline-block w-11/12">
      {inputFields.map((field) => <>{field}</>)}    
      </div>
      <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
        <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/></div>
        <div className="pl-2 font-bold">{saveOk}</div>
        <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
      </div> 
    </div>
  );
}
