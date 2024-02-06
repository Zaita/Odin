import { useRef, useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function SimpleTextField(props) {
  const [value, setValue] = useState(props.value != undefined ? props.value : "");

  let {label, submitCallback, siteConfig} = props;

  let fieldId = props.camalCase ? camalCase(label) : label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  
  // When Enter key is pressed, call submitcallback
  function handleKeyPress (e) {    
    if (e.key == 'Enter') {
      submitCallback();
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
    props.handleChange(fieldId, e.target.value);
    setValue(e.target.value);
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
      <input type="text" className="bg-white" name={camalCase(fieldId)} id={fieldId} value={value} onKeyUp={handleKeyPress} onChange={handleChange}
        style={{
          borderColor: siteConfig.theme_header_color,
          width: "100px"
        }}
        autoComplete={camalCase(fieldId)}/>   
    </div>
  )
}