
import React, { useRef, useState } from 'react';

import ThemedButton from "@/Components/ThemedButton";
import CheckBoxField from "@/Components/CheckBoxField";
import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";
import Admin_TextField from './Admin.TextField';
import Admin_DropdownField from './Admin.DropdownField';
import Admin_CheckBox from './Admin.Checkbox';

export default function Admin_InputAddScreen(props) {
  let [errors, setErrors] = useState();
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [renderFlag, setRenderFlag] = useState("");
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function handleChange(id, value) {
    userAnswers.current[id] = value;
    if (id == "input_type") {
      setRenderFlag(value);   
    }
  }

  function saveCallback() {
    SaveAnswersWithId(props.createRoute, props.createRouteParameters, setSaveOk, setErrors, userAnswers.current);
  }

  let labelField = { 
    "label" : "Label",
    "type": "text",
    "required": true,
  }

  let inputTypeField = {
    "label" : "Input Type",
    "type": "dropdown",
    "required": true,
    "options": ["", "text", "email", "textarea", "richtextarea", "dropdown", "date", "url", "checkbox", "radio" ],
  }

  let requiredField = {
    "label" : "Required",
    "type": "checkbox",
    "visibility" : true
  }

  let minLengthField = {
    "label" : "Min Length",
    "type": "text",
    "placeholder": "0",
    "required": false,
  }

  let maxLengthField = {
    "label" : "Max Length",
    "type": "text",
    "placeholder": "256",    
    "required": false,
  }

  let placeholderField = {
    "label" : "Placeholder",
    "type": "text",
    "placeholder": "",
    "required": false,
  }

  let productNameField = {
    "label" : "Product Name",
    "type": "checkbox",
  }

  let businessOwnerField = {
    "label" : "Business Owner",
    "type": "checkbox",
  }

  let ticketURLField = {
    "label" : "Ticket URL",
    "type": "checkbox",
  }

  let releaseDateField = {
    "label" : "Release Date",
    "type": "checkbox",
  }

  let serviceNameField = {
    "label": "Service Name",
    "type": "checkbox,"
  }

  return (
    <div className="pt-1 pb-2">
      <div className="font-bold pb-2">Add New Input Field</div>
        <div className="inline-block w-1/2"> 
          {/* Label */}
          <div className="w-full">
            <Admin_TextField field={labelField} value={labelField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide/>
          </div>
          {/* Input Type */}
          <div className="w-full">
            <Admin_DropdownField field={inputTypeField} value={inputTypeField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} options={inputTypeField.options} dbFormat sideBySide />
          </div>  
          {/* Field Required */}
          <div>
            <Admin_CheckBox field={requiredField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide runInit/>
          </div>
          {/* Minimum Length */}
          <div className="w-full">
            <Admin_TextField field={minLengthField} value={minLengthField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>
          {/* Maximum Length */}
          <div className="w-full">
            <Admin_TextField field={maxLengthField} value={maxLengthField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>
          {/* Place Holder Length */}
          <div className="w-full">
            <Admin_TextField field={placeholderField} value={placeholderField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>
        </div> {/* <div className="inline-block w-1/2">  */}
        <div className="inline-block align-top">
          <div><b>Special Fields:</b></div>

          {props.isPillar && renderFlag == "text" && <div>
            <CheckBoxField field={productNameField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}
          
          {props.isPillar && renderFlag == "email" && <div>
              <CheckBoxField field={businessOwnerField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}

          {props.isPillar && renderFlag == "url" && <div>
            <CheckBoxField field={ticketURLField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}

          {props.isPillar && renderFlag == "date" && <div>
            <CheckBoxField field={releaseDateField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}  

          {props.isTask && renderFlag == "text" && <div>
            <CheckBoxField field={serviceNameField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}              
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
          <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
        </div>  
    </div>
  )
}