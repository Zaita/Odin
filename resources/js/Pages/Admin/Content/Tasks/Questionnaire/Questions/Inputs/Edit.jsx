
import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from "@/Components/TextField";
import DropdownField from "@/Components/DropdownField";
import ThemedButton from "@/Components/ThemedButton";
import CheckBoxField from "@/Components/CheckBoxField";
import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";

export default function EditAnswerInputField(props) {
  console.log("Pillar.Question.Inputs.Edit");
  let [saveErrors, setSaveErrors] = useState(props.errors);
  let [saveOk, setSaveOk] = useState(null);
  let [renderFlag, setRenderFlag] = useState(true);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    console.log(`Input.edit = id: ${id}; value: ${value}`);
    userAnswers.current[id] = value;
    if (id == "input_type") {
      setRenderFlag(!renderFlag);   
    }
  }

  function saveAnswersCallback() {
    userAnswers.current["pillarId"] = props.pillar.id;
    userAnswers.current["questionId"] = props.question.id;
    SaveAnswersWithId("admin.content.pillar.question.input.save", {id:props.pillar.id, questionId:props.question.id, inputId:props.field.id}, 
      setSaveOk, setSaveErrors, userAnswers.current);
  }

  function getValue(targetProp, label) {
    if (userAnswers.current[label] == null || userAnswers.current[label] == "") 
      userAnswers.current[label] = targetProp;
    return userAnswers.current[label] ? userAnswers.current[label] : (targetProp ?  targetProp : "");
  }

  let labelField = { 
    "label" : "Label",
    "placeholder": "",
    "required": true,
    "value": getValue(props.field?.label, "label")
  }

  let inputTypeField = {
    "label" : "Input Type",
    "value": getValue(props.field?.input_type, "input_type")
  }

  let inputTypeOptions = [ "", "text", "email", "textarea", "rich text editor", "dropdown", "date", "url", "radio button", "checkbox" ];

  let requiredField = {
    "label" : "Required",
    "value" : getValue(props.field?.required, "required"),
    "visibility" : true
  }

  let minLengthField = {
    "label" : "Min Length",
    "placeholder": "0",
    "required": false,
    "value": getValue(props.field?.min_length, "min_length")
  }

  let maxLengthField = {
    "label" : "Max Length",
    "placeholder": "256",
    "required": false,
    "value": getValue(props.field?.max_length, "max_length")
  }

  let placeHolderField = {
    "label" : "Placeholder",
    "placeholder": "256",
    "required": false,
    "value": getValue(props.field?.placeholder, "placeholder")
  }

  let productNameField = {
    "label" : "Product Name",
    "value" : props.field?.product_name ? props.field.product_name : (userAnswers.current["product_name"] ? userAnswers.current["product_name"] : false),
    "visibility" : inputTypeField.value == "text"    
  }

  let businessOwnerField = {
    "label" : "Business Owner",
    "value" : props.field?.business_owner ? props.field.business_owner : (userAnswers.current["business_owner"] ? userAnswers.current["business_owner"] : false),
    "visibility" : inputTypeField.value == "email"    
  }

  let ticketURLField = {
    "label" : "Ticket Url",
    "value" : props.field?.ticket_url ? props.field.ticket_url : (userAnswers.current["ticket_url"] ? userAnswers.current["ticket_url"] : false),
    "visibility" : inputTypeField.value == "url"    
  }

  let releaseDateField = {
    "label" : "Release Date",
    "value" : props.field?.release_date ? props.field.release_date : (userAnswers.current["release_date"] ? userAnswers.current["release_date"] : false),
    "visibility" : inputTypeField.value == "date"    
  }

  function MyContent() {
    return (
      <div>      
        <div id="question_input_field" className="w-full">
          <div className="inline-block w-1/2"> 
            {/* Label */}
            <div className="w-full">
              <TextField field={labelField} value={labelField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
            {/* Input Type */}
            <div className="w-full">
              <DropdownField field={inputTypeField} value={inputTypeField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={inputTypeOptions} dbFormat sideBySide/>
            </div>  
            {/* Field Required */}
            <div>
              <CheckBoxField field={requiredField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
            {/* Minimum Length */}
            <div className="w-full">
              <TextField field={minLengthField} value={minLengthField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
            {/* Maximum Length */}
            <div className="w-full">
              <TextField field={maxLengthField} value={maxLengthField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
            {/* Place Holder Length */}
            <div className="w-full">
              <TextField field={placeHolderField} value={placeHolderField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
          </div> {/* <div className="inline-block w-1/2">  */}
          <div className="inline-block align-top">
            <div><b>Special Fields:</b></div>
            {/* Product Name */}
            <div>
              <CheckBoxField field={productNameField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>
            {/* Business Owner */}
            <div>
              <CheckBoxField field={businessOwnerField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>  
            {/* Ticket URL */}
            <div>
              <CheckBoxField field={ticketURLField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>   
            {/* Release Date */}
            <div>
              <CheckBoxField field={releaseDateField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
            </div>   
            {/* ----- */}                             
          </div>
          <div id="bottom_menu" className="h-10 pt-2">
          <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save Changes"/>
          <p>{saveOk}</p>
        </div>  
        </div>     
      </div>
    )
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}