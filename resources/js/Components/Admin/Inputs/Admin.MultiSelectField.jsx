import { useState, useEffect } from 'react';
import Select from 'react-select'

import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from "@/Utilities/dbFormat.jsx"

export default function Admin_MultiSelectField(props) {
  const [isFocused, setIsFocused] = useState(false);
  const [value, setValue] = useState(props.field.value == null ? "" : props.field.value);
  
  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;  
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";

  /**
   * 
   */
  function handleKeyPress (e) {
    // This is perfectly safe in react, it correctly detect the keys
    if (e.key == 'Enter') {
       props.submitCallback();
    }
  }

  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(values, action) {
    props.handleChange(fieldId, values)
    setValue(values);
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    if (props.runInit) {
      props.handleChange(fieldId, value);
    }
  })

  let abc = [];
  props.field.options.map((task) => {
    console.log(task);
    abc.push({ value: task, label: task })
  })

  return (
    <div id="input_field">
      <div id="label" 
      style={{
        display: props.sideBySide ? "inline-block" : "block",
        width: props.sideBySide ? "192px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <Select isMulti className="bg-white" name={camalCase(fieldId)} id={fieldId} onKeyUp={handleKeyPress}
        onChange={handleChange} defaultValue={value} options={abc}
        styles={{
          control: (baseStyles, state) => ({
            ...baseStyles,
            backgroundColor: props.siteConfig.theme_input_bg_color,
            color: props.siteConfig.theme_input_text_color,
            borderColor: props.siteConfig.theme_input_border_color,
            width: props.sideBySide ? "500px" : "355px",
            boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none", 
          }),
          container: (baseStyles, state) => ({
            ...baseStyles,
            display: "inline-block",
            backgroundColor: props.siteConfig.theme_input_bg_color,
            color: props.siteConfig.theme_input_text_color,
            borderColor: props.siteConfig.theme_input_border_color,
            width: props.sideBySide ? "500px" : "355px",
            boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none", 
          }),
          }}        
          onBlur={() => setIsFocused(false)}
          onFocus={() => setIsFocused(true)}
        />   
        <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}