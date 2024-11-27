import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function Admin_TextFieldReadOnly(props) {
  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;  

  return (
    <div id="input_field" className="h-7 pt-1">
      <div id="label" 
      style={{
        display: props.sideBySide ? "inline-block" : "block",
        width: props.sideBySide ? "192px" : "auto",
        }}> 
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <span style={{
          backgroundColor: props.siteConfig.theme_input_bg_color,
          color: props.siteConfig.theme_input_text_color,
          borderColor: props.siteConfig.theme_input_border_color,
          width: props.sideBySide ? "500px" : "355px",
          boxShadow: "1px 1px 0px 2px none",
          height: "30px",
        }}        
        >{props.field.value}</span>
    </div>
  )
}