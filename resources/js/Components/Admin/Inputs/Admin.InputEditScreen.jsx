
import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

import ThemedButton from "@/Components/ThemedButton";
import CheckBoxField from "@/Components/CheckBoxField";
import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";
import Admin_TextField from './Admin.TextField';
import Admin_DropdownField from './Admin.DropdownField';
import Admin_CheckBox from './Admin.Checkbox';
import DraggableField from "@/Components/DraggableField";
import DeleteModal from '@/Components/Admin/DeleteModal';

export default function Admin_InputEditScreen(props) {
  let [errors, setErrors] = useState();
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [renderFlag, setRenderFlag] = useState(props.input.input_type);
  let [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function openConfirmationModal(cboption) {
    deleteTarget.current["id"] = cboption.id;
    deleteTarget.current["type"] = inputTypeField.value + " Option";
    deleteTarget.current["name"] = cboption.label;
    setDeleteDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    let routeParameters = {id:props.objectId, questionId:props.question.id, inputId:props.input.id, optionId:deleteTarget.current["id"]};
    SaveAnswersWithId(props.deleteCheckboxRoute, routeParameters, setSaveOk, setErrors, deleteTarget.current);
    setDeleteDialogIsOpen(false)
  }

  function handleChange(id, value) {
    userAnswers.current[id] = value;
    if (id == "input_type") {
      setRenderFlag(value);   
    }
  }

  function saveCallback() {
    
    SaveAnswersWithId(props.saveRoute, props.routeParameters, setSaveOk, setErrors, userAnswers.current);
  }

  let labelField = { 
    "label" : "Label",
    "type": "text",
    "required": true,
    "value": props.input.label
  }

  let inputTypeField = {
    "label" : "Input Type",
    "type": "dropdown",
    "required": true,
    "value": props.input.input_type,
    "options": ["text", "email", "textarea", "richtextarea", "dropdown", "date", "url", "checkbox", "radio" ],
  }

  let requiredField = {
    "label" : "Required",
    "type": "checkbox",
    "value": props.input.required,
  }

  let minLengthField = {
    "label" : "Min Length",
    "type": "text",
    "placeholder": "0",
    "required": false,
    "value": props.input.min_length,
  }

  let maxLengthField = {
    "label" : "Max Length",
    "type": "text",
    "placeholder": "256",    
    "required": false,
    "value": props.input.max_length,
  }

  let placeholderField = {
    "label" : "Placeholder",
    "type": "text",
    "placeholder": "",
    "required": false,
    "value": props.input.placeholder,
  }

  let productNameField = {
    "label" : "Product Name",
    "type": "checkbox",        
    "value": props.input.product_name,
  }

  let businessOwnerField = {
    "label" : "Business Owner",
    "type": "checkbox",
    "value": props.input.business_owner, 
  }

  let ticketURLField = {
    "label" : "Ticket URL",
    "type": "checkbox",
    "value": props.input.ticket_url,
  }

  let releaseDateField = {
    "label" : "Release Date",
    "type": "checkbox",
    "value": props.input.release_date,
  }

  let serviceNameField = {
    "label": "Service Name",
    "type": "checkbox,",
    "value": props.input.service_name,
  }

  /**
     * Handle doing shit if the input type is a check box.
     * We need to handle
     * - reodering checkbox list items
     * - build links to edit and delete routes
     */
  let [inputOption, setInputOption] = useState(props.input.input_options);
  let checkboxSortCallback = async (draggedFrom, draggedTo) => {    
    console.log(draggedTo);
    const itemDragged = inputOption[draggedFrom];
    const remainingItems = inputOption.filter((item, index) => index !== draggedFrom);
    // Rebuild our list splicing in the new item that we copied above.
    setInputOption([
      ...remainingItems.slice(0, draggedTo),
      itemDragged,
      ...remainingItems.slice(draggedTo)
    ]);
    // Don't save it yet, we'll wait til user presses save
    setSaveOk("Unsaved changes");
  }

  let checkboxConfig = null;
  let inputOptionsList = [];
  if (inputTypeField.value == "checkbox" || inputTypeField.value == "radio") {    
    props.input.input_options.map((option, key) => {
    let editParameters = {id:props.objectId, questionId:props.question.id, inputId:props.input.id, optionId:option.id};
    inputOptionsList.push(
      <div className="pt-1 h-fit overflow-y-hidden"
        style={{
          padding: "2px",
          borderColor: props.siteConfig.theme_admin_content_spacer,
          borderWidth: "1px",
          marginBottom: "2px",
        }}>
        <div className="w-6 float-left"><DragIndicatorIcon/></div>
        <div className="w-5/6 float-left pt-1">{option.label}</div>
        <div> 
          <EditIcon className="cursor-pointer" onClick={() => router.get(route(props.editCheckboxRoute, editParameters))}/> 
          <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(option)}/>
        </div>
      </div>          
    )});

    checkboxConfig = 
      <div>
        <div><span className="font-bold">{inputTypeField.value} Options:</span></div>
        <DraggableField items={inputOptionsList} callback={checkboxSortCallback} siteConfig={props.siteConfig}/>
        <div><ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route(props.addCheckboxRoute, props.routeParameters))} children="Add Option" className="mr-4"/></div>
      </div>
  }


  return (
    <div className="pt-1 pb-2">
      <DeleteModal open={deleteDialogIsOpen} itemInfo={deleteTarget.current} onConfirm={confirmedDeletion}
      onCancel={() => setDeleteDialogIsOpen(false)} {...props}/>      
      <div className="font-bold pb-2">Modify Existing Input Field</div>
        <div className="inline-block w-1/2"> 
          {/* Label */}
          <div className="w-full">
            <Admin_TextField field={labelField} value={labelField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide runInit/>
          </div>
          {/* Input Type */}
          <div className="w-full">
            <Admin_DropdownField field={inputTypeField} value={inputTypeField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} options={inputTypeField.options} dbFormat sideBySide runInit/>
          </div>  
          {/* Field Required */}
          <div>
            <Admin_CheckBox field={requiredField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide runInit/>
          </div>
          {/* Minimum Length */}
          <div className="w-full">
            <Admin_TextField field={minLengthField} value={minLengthField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>
          {/* Maximum Length */}
          <div className="w-full">
            <Admin_TextField field={maxLengthField} value={maxLengthField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>
          {/* Place Holder Length */}
          <div className="w-full">
            <Admin_TextField field={placeholderField} value={placeholderField.value} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>
        </div> {/* <div className="inline-block w-1/2">  */}
        <div className="inline-block align-top">
          <div><b>Special Fields:</b></div>

          {props.isPillar && renderFlag == "text" && <div>
            <CheckBoxField field={productNameField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>}
          
          {props.isPillar && renderFlag == "email" && <div>
              <CheckBoxField field={businessOwnerField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>}

          {props.isPillar && renderFlag == "url" && <div>
            <CheckBoxField field={ticketURLField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>}

          {props.isPillar && renderFlag == "date" && <div>
            <CheckBoxField field={releaseDateField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide runInit/>
          </div>}  
          
          {props.isTask && renderFlag == "text" && <div>
            <CheckBoxField field={serviceNameField} submitCallback={saveCallback}
                handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>}             
        </div>
        <div>
          {(renderFlag == "checkbox" || renderFlag == "radio") && checkboxConfig}
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
          <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
        </div>  
    </div>
  )
}