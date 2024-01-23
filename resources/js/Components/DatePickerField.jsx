
import { useState, useEffect } from 'react';
import DatePicker from "react-datepicker";
import ReportIcon from '@mui/icons-material/Report';

export default function DatePickerField(props) {
  const [value, setValue] = useState(props.value);

  /**
   * Handle the change in the date picker locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * 
   */
  function handleChange(value) {
    const id = props.field.label;
    setValue(value);
    props.handleChange(id, value);
  }

  /**
   * Re-Populate after a fresh load/render
   * This is because programatically changing field value
   * in a form error won't trigger onChange
   */
  useEffect(() => {
    if (props.runInit) {
      props.handleChange(props.field.label, value);
    }
 })

 let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;
 let error = props.errors && props.field.label in props.errors ? (<><ReportIcon/> {props.errors[props.field.label]}</>) : "";
 const selected = value != "" && value != null ? new Date(value) : "";
 
  return (
    <div id="input_field">
      <div id="label"><label htmlFor={props.field.label}>{label}</label></div>
      <style>{`
        .react-datepicker__input-container input {
          border: 1px solid ${props.siteConfig.theme_header_color};
        }     
      `}    
      </style>
      <DatePicker id={props.field.label} dateFormat="dd/MM/yyyy" selected={selected} onChange={(date) => handleChange(date)} dropdownMode="scroll" withPortal/>
      <p id="error" style={{color: props.siteConfig.theme_subheader_color}}>{error}</p> 
    </div>
  )
}