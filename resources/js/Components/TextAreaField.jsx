
import { useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function TextAreaField(props) {
  const [value, setValue] = useState(props.value);
  const [isFocused, setIsFocused] = useState(false);

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";
  let newHeight = props.height ? props.height : "275px";

  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(e) {
    props.handleChange(fieldId, e.target.value);
    setValue(e.target.value);
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    if (props.runInit) {
      props.handleChange(fieldId, value);
    }
  })

  return (
    <div id="input_field">
      <div id="label"><label htmlFor={fieldId}>{label}</label></div>
      <textarea className="bg-white" id={fieldId} value={value} 
        placeholder={props.field.placeHolder} onChange={handleChange} 
        style={{
          backgroundColor: props.siteConfig.theme_input_bg_color,
          color: props.siteConfig.theme_input_text_color,
          borderColor: props.siteConfig.theme_input_border_color,
          boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none",          
          height: newHeight,
          width: "100%"}}
          onBlur={() => setIsFocused(false)}
          onFocus={() => setIsFocused(true)}
          />   
      <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}