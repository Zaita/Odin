
import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import DraggableList from '@/Components/DraggableField';

export default function ActionList(props) {
  console.log("Admin.Content.Pillar.Question.ActionList");
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  let [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let [fields, setFields] = useState(props.question.action_fields);

  let {addRoute, saveOrderRoute, editRoute, deleteRoute}  = {...props}

  let newFields = props.question.action_fields;
  if (newFields.length != fields.length) {
    setFields(newFields);
  }

  /**
   * Handle our deletion confirmation modal
   * @param {} target 
   */
  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Action Field";
    deleteTarget.current["name"] = target.label;
    setDeleteDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId(deleteRoute, {id:props.pillar.id, questionId:props.question.id, actionId:deleteTarget.current["id"]}, setSaveOk, setSaveErrors, deleteTarget.current);
    setDeleteDialogIsOpen(false)
  }

  /**
   * Build list of questions to be passed to Draggable.
   * The Question.Title is unique, so we use this as an identifier
   */
  let fieldList = [];
  {fields && fields.map && fields.map((field, index) => {
    let tasks = field.tasks;
    fieldList.push(
      <div key={"aa"+index} style={{border: "1px solid white"}} className="pt-1 h-fit overflow-y-hidden">
        <div className="w-6 float-left"><DragIndicatorIcon/></div>
        <div className="w-1/6 float-left pt-1">{field.label}</div>
        <div className="w-1/6 float-left pt-1">{field.action_type}</div>
        <div className="w-1/6 float-left pt-1">{field.action_type == "goto" ? field.goto_question_title : "-"}</div>
        <div className="w-2/6 float-left pt-1">{tasks && tasks.length > 0 ?
          tasks.map((item, index2) => <span key={"uu"+index2}>{item.name}</span>).reduce((result, item) => [result, <br/>, item]) : "-"}
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
    const itemDragged = fields[draggedFrom];
    const remainingItems = fields.filter((item, index) => index !== draggedFrom);
    // Rebuild our list splicing in the new item that we copied above.
    setFields([
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
    fields.map((field) => titleList.push(field.id));
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
      children="Add Action" className="mr-4"/>
    <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    <div>
      <div>
      <div className="w-6 float-left">&nbsp;</div>
        <div className="w-1/6 float-left pt-1 font-bold">Label</div>
        <div className="w-1/6 float-left pt-1 font-bold">Type</div>
        <div className="w-1/6 float-left pt-1 font-bold">Goto Question</div>
        <div className="w-2/6 float-left pt-1 font-bold">Tasks</div>
        <div>Actions</div>
      </div>
      <div style={{borderBottom: "3px solid white"}}>&nbsp;</div>
      <DraggableList siteConfig={props.siteConfig} items={fieldList} callback={sortCallback}/>          
    </div>    
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={props.siteConfig} onClick={SaveOrder} children="Save Action Field Order" className="mr-4"/>
      <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    </div>          
    </>
  );
}