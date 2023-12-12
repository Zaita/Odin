
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
    if (id == "inputType") {
      setRenderFlag(!renderFlag);   
    }
  }

  function saveAnswersCallback() {
    userAnswers.current["pillarId"] = props.pillar.id;
    userAnswers.current["questionId"] = props.question.index;
    SaveAnswersWithId("admin.content.pillar.question.input.save", {id:props.pillar.id, questionId:props.question.index, inputId:props.field.index}, 
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
    "value": getValue(props.field?.inputType, "inputType")
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
    "value": getValue(props.field?.minLength, "minLength")
  }

  let maxLengthField = {
    "label" : "Max Length",
    "placeholder": "256",
    "required": false,
    "value": getValue(props.field?.maxLength, "maxLength")
  }

  let placeHolderField = {
    "label" : "Placeholder",
    "placeholder": "256",
    "required": false,
    "value": getValue(props.field?.placeHolder, "placeHolder")
  }

  let productNameField = {
    "label" : "Product Name",
    "value" : props.field?.productName ? props.field.productName : (userAnswers.current["productName"] ? userAnswers.current["productName"] : false),
    "visibility" : inputTypeField.value == "text"    
  }

  let businessOwnerField = {
    "label" : "Business Owner",
    "value" : props.field?.businessOwner ? props.field.businessOwner : (userAnswers.current["businessOwner"] ? userAnswers.current["businessOwner"] : false),
    "visibility" : inputTypeField.value == "email"    
  }

  let ticketURLField = {
    "label" : "Ticket Url",
    "value" : props.field?.ticketUrl ? props.field.ticketUrl : (userAnswers.current["ticketUrl"] ? userAnswers.current["ticketUrl"] : false),
    "visibility" : inputTypeField.value == "url"    
  }

  let releaseDateField = {
    "label" : "Release Date",
    "value" : props.field?.releaseDate ? props.field.releaseDate : (userAnswers.current["releaseDate"] ? userAnswers.current["releaseDate"] : false),
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
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
            {/* Input Type */}
            <div className="w-full">
              <DropdownField field={inputTypeField} value={inputTypeField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={inputTypeOptions} camalCase sideBySide/>
            </div>  
            {/* Field Required */}
            <div>
              <CheckBoxField field={requiredField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
            {/* Minimum Length */}
            <div className="w-full">
              <TextField field={minLengthField} value={minLengthField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
            {/* Maximum Length */}
            <div className="w-full">
              <TextField field={maxLengthField} value={maxLengthField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
            {/* Place Holder Length */}
            <div className="w-full">
              <TextField field={placeHolderField} value={placeHolderField.value} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
          </div> {/* <div className="inline-block w-1/2">  */}
          <div className="inline-block align-top">
            <div><b>Special Fields:</b></div>
            {/* Product Name */}
            <div>
              <CheckBoxField field={productNameField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>
            {/* Business Owner */}
            <div>
              <CheckBoxField field={businessOwnerField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>  
            {/* Ticket URL */}
            <div>
              <CheckBoxField field={ticketURLField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
            </div>   
            {/* Release Date */}
            <div>
              <CheckBoxField field={releaseDateField} submitCallback={saveAnswersCallback}
                  handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase sideBySide/>
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