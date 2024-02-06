
import { useState, useEffect, useRef } from 'react';
import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function Form_CheckBoxOption(props) {
  const [isFocused, setIsFocused] = useState([]);
  const [checked, setChecked] = useState(props.checked);

  let fieldId = props.camalCase ? camalCase(props.label) : props.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
  let label = props.label; 

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
  function handleChange(object) {
    props.handleChange(label, !checked);
    setChecked(!checked);    
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    props.handleChange(label, checked);
  });

  return (
    <div className="inline-block w-10/12 pb-2">
      <div>
        <input type="checkbox" className="bg-white" name={fieldId} id={label} value={props.value} onChange={handleChange} checked={checked}
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
            <span className="pl-2">{props.label}</span>
      </div>
    </div>
  )
}