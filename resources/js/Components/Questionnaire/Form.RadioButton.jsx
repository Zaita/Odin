
import { useRef, useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function Form_RadioButton(props) {
  let userValues = useRef(props.value);
  let [renderFlag, setRenderFlag] = useState(false);

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label; 
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";
  
  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(newValue) {
    userValues.current = newValue.target.defaultValue;
    props.handleChange(fieldId, newValue.target.defaultValue);
    setRenderFlag(!renderFlag);
  }

  // Run once after object loads.
  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    if (props.runInit) {
      props.handleChange(fieldId, userValues.current);
    }
  })

  return (
    <div id="input_field">
      <div id="label"><label htmlFor={fieldId}>{label}</label></div>
      {props.field.input_options?.map((option, index) => 
      <div key={"rb_" + index}>
        <input type="radio" name={fieldId} value={option.value} onChange={handleChange}
          checked={userValues.current == option.value}
          style={{
            // backgroundColor: props.siteConfig.theme_input_bg_color,
            // color: props.siteConfig.theme_input_text_color,
            // borderColor: props.siteConfig.theme_input_border_color,
            // boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none",  
            width: "18px",
            height: "18px",
            }}
            // onBlur={() => setIsFocused(false)}
            // onFocus={() => setIsFocused(true)}
            />
            <span className="pl-2">{option.label}</span>
      </div>
      )}
      <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}