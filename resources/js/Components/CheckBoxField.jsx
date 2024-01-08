
import { useRef, useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function CheckBoxField(props) {
  const [value, setValue] = useState(props.field.value != undefined ? props.field.value : "");

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
    props.handleChange(fieldId, !value);
    setValue(!value);
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    if (props.runInit) {
      props.handleChange(fieldId, value);
    }
  })

  if (props.field.visibility) {
    return (
      <div id="input_field">
        <div id="label" 
        style={{
          display: props.sideBySide ? "inline-block" : "block",
          width: props.sideBySide ? "125px" : "auto",
          }}> 
          <label htmlFor={fieldId}>{label}</label>
        </div>
        <input type="checkbox" className="bg-white" name={camalCase(fieldId)} id={fieldId} onKeyUp={handleKeyPress}
          placeholder={props.field.placeHolder} onChange={handleChange} defaultChecked={props.field.value}
          style={{
            borderColor: props.siteConfig.themeHeaderColor,
          }}
          autoComplete={camalCase(fieldId)}/>   
        <p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p> 
      </div>
    )
  } else {
    if(value == true && (props.field.value == undefined || props.field.value == "" || props.field.value == false)) {
      handleChange(false);
    }
    return(<></>)
  }
}