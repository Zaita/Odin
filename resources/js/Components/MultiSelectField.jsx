
import { useState, useEffect } from 'react';
import Select from 'react-select'

import ReportIcon from '@mui/icons-material/Report';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from "@/Utilities/dbFormat.jsx"
import { Block } from '@mui/icons-material';

export default function MultiSelectField(props) {
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
  //   { value: 'chocolate', label: 'Chocolate' },
  //   { value: 'strawberry', label: 'Strawberry' },
  //   { value: 'vanilla', label: 'Vanilla' }
  // ];

  props.options.map((task) => {
    console.log(task);
    abc.push({ value: task, label: task })
  })

  return (
    <div id="input_field">
      <div id="label" style={{
        display: props.sideBySide ? "inline-block" : "block",
        width: props.sideBySide ? "100px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <Select isMulti className="bg-white" name={camalCase(fieldId)} id={fieldId} onKeyUp={handleKeyPress}
        onChange={handleChange} defaultValue={value}options={abc}
        styles={{
          control: (baseStyles, state) => ({
            ...baseStyles,
            borderColor: props.siteConfig.theme_header_color, 
            width: newWidth
          }),
          container: (baseStyles, state) => ({
            ...baseStyles,
            display: "inline-block",
            borderColor: props.siteConfig.theme_header_color, 
            width: newWidth
          }),
          }}        
        />
      <p id="error" style={{color: props.siteConfig.theme_subheader_color}}>{error}</p> 
    </div>
  )
}