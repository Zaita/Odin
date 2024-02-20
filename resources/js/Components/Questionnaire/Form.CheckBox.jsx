
import { useState, useEffect, useRef } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

import Form_CheckBoxOption from './Form.CheckBoxOption';

export default function Form_CheckBox(props) {
  let userValues = useRef({});

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label; 
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";
  
  // Load initial values into setValue
  // if (Object.keys(userValues.current).length == 0) {
  props.field.input_options?.map((option) => {
    if (!props.values) {
      userValues.current[option.label] = false;
    } else {
      let tempValue = props.values[option.label] != undefined ? props.values[option.label] : false;
      userValues.current[option.label] = tempValue;
    }
  });
  // }

  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(objectId, newValue) {
    props.handleChange(props.field.label, objectId, newValue);
  }

  return (
    <div id="input_field">
      <div id="label"><label htmlFor={fieldId}>{label}</label></div>
      {props.field.input_options?.map((option, index) => 
        <Form_CheckBoxOption key={index} label={option.label} value={option.value} handleChange={handleChange}
          checked={userValues.current[option.label]} />
      )}
      <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}