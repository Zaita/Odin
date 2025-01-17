import { useState, useEffect } from 'react';
// import { HexColorPicker } from "react-colorful";
import { ChromePicker } from 'react-color'

import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function ColorField(props) {
  const [value, setValue] = useState(props.value != undefined ? props.value : "");

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;  
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";
  let type = props.type ? props.type : "text";
  
  /**
   * How to setup popup later:
   * http://casesandberg.github.io/react-color/#examples
   */

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
  function handleChange(newValue) {
    props.handleChange(fieldId, newValue.hex);
    setValue(newValue.hex);
  }

  // Run once after object loads.
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
        width: props.sideBySide ? "100px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <ChromePicker  type={type} className="bg-white" name={camalCase(fieldId)} id={fieldId} color={value} onKeyUp={handleKeyPress}
        onChange={handleChange}
        style={{
          borderColor: props.siteConfig.theme_header_color,
          width: props.sideBySide ? "400px" : "355px"
        }}
        autoComplete={camalCase(fieldId)}/>   
      <p id="error" style={{color: props.siteConfig.theme_subheader_color}}>{error}</p> 
    </div>
  )
}