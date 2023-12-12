
import { useState, useEffect } from 'react';
import ContentEditable from 'react-contenteditable'
import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"

export default function RichTextAreaField(props) {
  const [value, setValue] = useState({
    html: props.value,
    editable: true
  });

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;
  let error = props.errors && props.field.label in props.errors ? (<><ReportIcon/> {props.errors[props.field.label]}</>) : "";
  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(e) {
    const value = e.target.value
    setValue({html: value});
    props.handleChange(fieldId, value.html);
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    props.handleChange(fieldId, value.html);
  })

  return (
    <div id="input_field">
      <div id="label"><label htmlFor={fieldId}>{label}</label></div>
      <ContentEditable
              className="bg-white"
              id={fieldId}
              html={value.html} // innerHTML of the editable div
              onChange={handleChange} // handle innerHTML change
              style={{borderColor: props.siteConfig.themeHeaderColor, height: "275px", width: "100%"}}
        />   
      <p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p> 
    </div>
  )
}