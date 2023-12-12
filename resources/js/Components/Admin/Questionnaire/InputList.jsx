
import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import DraggableList from '@/Components/DraggableField';

export default function InputList(props) {
  console.log("Admin.Content.Pillar.Question.Inputs");
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  let [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let [inputs, setInputs] = useState(props.question.input_fields);

  let {addRoute, saveOrderRoute, editRoute, deleteRoute}  = {...props}
  

  let newInputs = props.question.input_fields;
  if (newInputs.length != inputs.length) {
    setInputs(newInputs);
  }

  /**
   * Handle our deletion confirmation modal
   * @param {} target 
   */
  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Input Field";
    deleteTarget.current["name"] = target.label;
    setDeleteDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId(deleteRoute, {id:props.pillar.id, questionId:props.question.id, inputId:deleteTarget.current["id"]}, setSaveOk, setSaveErrors, deleteTarget.current);
    setDeleteDialogIsOpen(false)
  }

  /**
   * Build list of questions to be passed to Draggable.
   * The Question.Title is unique, so we use this as an identifier
   */
  let inputList = [];
  {inputs && inputs.map && inputs.map((field, index) => {inputList.push(
      <div style={{border: "1px solid white"}} className="pt-1 h-fit overflow-y-hidden">
        <div className="w-6 float-left"><DragIndicatorIcon/></div>
        <div className="w-1/6 float-left pt-1">{field.label}</div>
        <div className="w-1/6 float-left pt-1">{field.input_type}</div>
        <div className="w-3/6 float-left pt-1">{field.product_name == true ? "Product name" : ""}
          {field.business_owner == true ? "Business owner" : ""}
          {field.ticket_url == true ? "Ticket url" : ""}
          &nbsp;        
        </div>
        <div> 
          <EditIcon className="cursor-pointer" onClick={() => router.get(route(editRoute, [props.pillar.id, props.question.id, field.id]))}/> 
          <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(field)}/>
        </div>
      </div>
    )});
  }

  /** 
   * Create a callback for when the draggable list handles a drop.
   * This allows us to re-order our questions list.
   */
  let sortCallback = async (draggedFrom, draggedTo) => {  
    console.log("Sort Callback");
    console.log(draggedTo);
    const itemDragged = inputs[draggedFrom];
    const remainingItems = inputs.filter((item, index) => index !== draggedFrom);
    // Rebuild our list splicing in the new item that we copied above.
    setInputs([
      ...remainingItems.slice(0, draggedTo),
      itemDragged,
      ...remainingItems.slice(draggedTo)
     ]);
     // Don't save it yet, we'll wait til user presses save
     setSaveOk("Unsaved changes");
  }

  /**
   * Navigate to the URL that will allow us to create new questions
   */
  function SaveOrder() {
    let titleList = [];
    inputs.map((question) => titleList.push(question.id));
    let newOrder = {
      "newOrder": titleList
    }

    // Save the new order
    SaveAnswersWithId(saveOrderRoute, {id:props.pillar.id, questionId:props.question.id}, setSaveOk, setSaveErrors, newOrder);
  }

  return(
    <>
    <DeleteModal open={deleteDialogIsOpen} itemInfo={deleteTarget.current} onConfirm={confirmedDeletion}
      onCancel={() => setDeleteDialogIsOpen(false)} {...props}/>
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route(addRoute, {id:props.pillar.id, questionId: props.question.id}))} 
      children="Add Input" className="mr-4"/>
    <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    <div>
      <div style={{borderBottom: "3px solid white"}}>&nbsp;</div>
      <DraggableList items={inputList} callback={sortCallback}/>          
    </div>    
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={props.siteConfig} onClick={SaveOrder} children="Save Input Field Order" className="mr-4"/>
      <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    </div>          
    </>
  );
}