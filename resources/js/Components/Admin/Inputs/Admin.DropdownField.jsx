import { useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from "@/Utilities/dbFormat.jsx"

export default function Admin_DropdownField(props) {
  const [isFocused, setIsFocused] = useState(false);
  const [value, setValue] = useState(props.field.value ? props.field.value : props.field.options[0]);

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
  function handleChange(e) {
    props.handleChange(fieldId, e.target.value, false);
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
      <div id="label" 
      style={{
        display: props.sideBySide ? "inline-block" : "block",
        width: props.sideBySide ? "192px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <select className="bg-white" name={camalCase(fieldId)} id={fieldId} onKeyUp={handleKeyPress}
        onChange={handleChange} 
        style={{
          backgroundColor: props.siteConfig.theme_input_bg_color,
          color: props.siteConfig.theme_input_text_color,
          borderColor: props.siteConfig.theme_input_border_color,
          width: props.sideBySide ? "500px" : "355px",
          boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none",    
        }}
        value={value}
        onBlur={() => setIsFocused(false)}
        onFocus={() => setIsFocused(true)}
        >   
          {props.field.options.map((option, index) => (<option key={index} label={option} value={option}/>))}
      </select>
      <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}