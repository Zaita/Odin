
// import React, { useRef, useState } from 'react';
// import { router } from '@inertiajs/react'
// import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
// import EditIcon from '@mui/icons-material/Edit';
// import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

// import TextField from "@/Components/TextField";
// import DropdownField from "@/Components/DropdownField";
// import ThemedButton from "@/Components/ThemedButton";
// import CheckBoxField from "@/Components/CheckBoxField";
// import { SaveAnswersWithId } from "@/Components/Admin/SaveAnswers";
// import DraggableField from "@/Components/DraggableField";
// import DeleteModal from '@/Components/Admin/DeleteModal';

// export default function EditInputField(props) {
//   console.log("Pillar.Question.Inputs.Edit");
//   let [saveErrors, setSaveErrors] = useState(props.errors);
//   let [saveOk, setSaveOk] = useState(null);
//   let [renderFlag, setRenderFlag] = useState(true);
//   let [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
//   let deleteTarget = useRef({});
//   let userAnswers = useRef([]);

//   function openConfirmationModal(question) {
//     deleteTarget.current["id"] = question.id;
//     deleteTarget.current["type"] = "Question";
//     deleteTarget.current["name"] = question.label;
//     setDeleteDialogIsOpen(true);
//   }

//   function confirmedDeletion() {
//     console.log("Deletion Confirmed");
//     // SaveAnswersWithId(deleteRoute, {id:objectId, questionId:deleteTarget.current["id"]}, setSaveOk, setSaveErrors, deleteTarget.current);
//     setDeleteDialogIsOpen(false)
//   }

//   function handleChange(id, value) {
//     console.log(`Input.edit = id: ${id}; value: ${value}`);
//     userAnswers.current[id] = value;
//     if (id == "input_type") {
//       setRenderFlag(!renderFlag);   
//     }
//   }

//   function saveAnswersCallback() {
//     userAnswers.current["pillarId"] = props.pillar.id;
//     userAnswers.current["questionId"] = props.question.id;
//     SaveAnswersWithId("admin.content.pillar.question.input.save", {id:props.pillar.id, questionId:props.question.id, inputId:props.field.id}, 
//       setSaveOk, setSaveErrors, userAnswers.current);
//   }

//   function getValue(targetProp, label) {
//     if (userAnswers.current[label] == null || userAnswers.current[label] == "") 
//       userAnswers.current[label] = targetProp;
//     return userAnswers.current[label] ? userAnswers.current[label] : (targetProp ?  targetProp : "");
//   }

//   let labelField = { 
//     "label" : "Label",
//     "placeholder": "",
//     "required": true,
//     "value": getValue(props.field?.label, "label")
//   }

//   let inputTypeField = {
//     "label" : "Input Type",
//     "value": getValue(props.field?.input_type, "input_type")
//   }

//   let inputTypeOptions = [ "", "text", "email", "textarea", "rich text editor", "dropdown", "date", "url", "radio button", "checkbox" ];

//   let requiredField = {
//     "label" : "Required",
//     "value" : getValue(props.field?.required, "required"),
//     "visibility" : true
//   }

//   let minLengthField = {
//     "label" : "Min Length",
//     "placeholder": "0",
//     "required": false,
//     "value": getValue(props.field?.min_length, "min_length")
//   }

//   let maxLengthField = {
//     "label" : "Max Length",
//     "placeholder": "256",
//     "required": false,
//     "value": getValue(props.field?.max_length, "max_length")
//   }

//   let placeHolderField = {
//     "label" : "Placeholder",
//     "placeholder": "256",
//     "required": false,
//     "value": getValue(props.field?.placeholder, "placeholder")
//   }

//   let productNameField = {
//     "label" : "Product Name",
//     "value" : props.field?.product_name ? props.field.product_name : (userAnswers.current["product_name"] ? userAnswers.current["product_name"] : false),
//     "visibility" : inputTypeField.value == "text"    
//   }

//   let businessOwnerField = {
//     "label" : "Business Owner",
//     "value" : props.field?.business_owner ? props.field.business_owner : (userAnswers.current["business_owner"] ? userAnswers.current["business_owner"] : false),
//     "visibility" : inputTypeField.value == "email"    
//   }

//   let ticketURLField = {
//     "label" : "Ticket Url",
//     "value" : props.field?.ticket_url ? props.field.ticket_url : (userAnswers.current["ticket_url"] ? userAnswers.current["ticket_url"] : false),
//     "visibility" : inputTypeField.value == "url"    
//   }

//   let releaseDateField = {
//     "label" : "Release Date",
//     "value" : props.field?.release_date ? props.field.release_date : (userAnswers.current["release_date"] ? userAnswers.current["release_date"] : false),
//     "visibility" : inputTypeField.value == "date"    
//   }

//   /**
//    * Handle doing shit if the input type is a check box.
//    * We need to handle
//    * - reodering checkbox list items
//    * - build links to edit and delete routes
//    */
//   let [checkboxOptions, setCheckboxOptions] = useState(props.field.checkbox_options);
//   let checkboxSortCallback = async (draggedFrom, draggedTo) => {    
//     console.log(draggedTo);
//     const itemDragged = checkboxOptions[draggedFrom];
//     const remainingItems = checkboxOptions.filter((item, index) => index !== draggedFrom);
//     // Rebuild our list splicing in the new item that we copied above.
//     setCheckboxOptions([
//       ...remainingItems.slice(0, draggedTo),
//       itemDragged,
//       ...remainingItems.slice(draggedTo)
//      ]);
//      // Don't save it yet, we'll wait til user presses save
//      setSaveOk("Unsaved changes");
//   }

//   let checkboxConfig = null;
//   let checkboxOptionsList = [];
//   if (inputTypeField.value == "checkbox") {    
//     props.field.checkbox_options.map((option, key) => {
//     let editParameters = {id:props.pillar.id, questionId:props.question.id, inputId:props.field.id, optionId:option.id};
//     checkboxOptionsList.push(
//       <div className="pt-1 h-fit overflow-y-hidden"
//         style={{
//           padding: "2px",
//           borderColor: props.siteConfig.theme_admin_content_spacer,
//           borderWidth: "1px",
//           marginBottom: "2px",
//         }}>
//         <div className="w-6 float-left"><DragIndicatorIcon/></div>
//         <div className="w-5/6 float-left pt-1">{option.label}</div>
//         <div> 
//           <EditIcon className="cursor-pointer" onClick={() => router.get(route(props.editCheckboxRoute, editParameters))}/> 
//           <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(option)}/>
//         </div>
//       </div>          
//     )});

//     checkboxConfig = 
//       <div>
//         <div><span className="font-bold">Checkbox Options:</span></div>
//         <DraggableField items={checkboxOptionsList} callback={checkboxSortCallback} siteConfig={props.siteConfig}/>
//         <div><ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route(props.addCheckboxRoute, props.routeParameters))} children="Add Option" className="mr-4"/></div>
//       </div>
//   }

//   /**
//    * Render
//    */  
//   return (
//     <div>      
//       <DeleteModal open={deleteDialogIsOpen} itemInfo={deleteTarget.current} onConfirm={confirmedDeletion}
//       onCancel={() => setDeleteDialogIsOpen(false)} {...props}/>
//       <div id="question_input_field" className="w-full">
//         <div className="inline-block w-1/2"> 
//           {/* Label */}
//           <div className="w-full">
//             <TextField field={labelField} value={labelField.value} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//           {/* Input Type */}
//           <div className="w-full">
//             <DropdownField field={inputTypeField} value={inputTypeField.value} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} options={inputTypeOptions} dbFormat sideBySide/>
//           </div>  
//           {/* Field Required */}
//           <div>
//             <CheckBoxField field={requiredField} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//           {/* Minimum Length */}
//           <div className="w-full">
//             <TextField field={minLengthField} value={minLengthField.value} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//           {/* Maximum Length */}
//           <div className="w-full">
//             <TextField field={maxLengthField} value={maxLengthField.value} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//           {/* Place Holder Length */}
//           <div className="w-full">
//             <TextField field={placeHolderField} value={placeHolderField.value} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//         </div> {/* <div className="inline-block w-1/2">  */}
//         <div className="inline-block align-top">
//           <div><b>Special Fields:</b></div>
//           {/* Product Name */}
//           <div>
//             <CheckBoxField field={productNameField} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>
//           {/* Business Owner */}
//           <div>
//             <CheckBoxField field={businessOwnerField} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>  
//           {/* Ticket URL */}
//           <div>
//             <CheckBoxField field={ticketURLField} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>   
//           {/* Release Date */}
//           <div>
//             <CheckBoxField field={releaseDateField} submitCallback={saveAnswersCallback}
//                 handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
//           </div>   
//           {/* ----- */}                             
//         </div>
//         <div>
//           {checkboxConfig}
//         </div>
//         <div id="bottom_menu" className="h-10 pt-2">
//         <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save Changes"/>
//         <p>{saveOk}</p>
//       </div>  
//       </div>     
//     </div>
//   ) 
// }