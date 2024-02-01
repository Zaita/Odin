
import { useState, useRef, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from "@/Utilities/dbFormat.jsx"

export default function DropdownField(props) {
  const [value, setValue] = useState(props.value);

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;  
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";
  let newWidth = props.width ? props.width : "355px";
  newWidth = props.sideBySide ? "400px" : newWidth;

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
      <div id="label" style={{
        display: props.sideBySide ? "inline-block" : "block",
        width: props.sideBySide ? "100px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <select className="bg-white" name={camalCase(fieldId)} id={fieldId} onKeyUp={handleKeyPress}
        onChange={handleChange} 
        style={{
          backgroundColor: props.siteConfig.theme_input_bg_color,
          color: props.siteConfig.theme_input_text_color,
          borderColor: props.siteConfig.theme_input_border_color,
          width: newWidth
        }}
        value={value}>   
        {props.options.map((option, index) => 
        (<option key={index} label={option} value={option}/>)
        )}
      </select>
      <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}