
import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DragIndicatorIcon from '@mui/icons-material/DragIndicator';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import DraggableList from '@/Components/DraggableField';

export default function QuestionsList(props) {
  console.log("Admin.QuestionsList");
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  let [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let [questions, setQuestions] = useState(props.questions);

  let {siteConfig, objectId, addRoute, saveOrderRoute, saveOrderParameters, editRoute, deleteRoute}  = {...props}
  
  let newQuestions = props.questions;
  if (newQuestions.length != questions.length) {
    setQuestions(newQuestions);
  }

  /**
   * Handle our deletion confirmation modal
   * @param {} target 
   */
  function openConfirmationModal(question) {
    deleteTarget.current["id"] = question.id;
    deleteTarget.current["type"] = "Question";
    deleteTarget.current["name"] = question.title;
    setDeleteDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId(deleteRoute, {id:objectId, questionId:deleteTarget.current["id"]}, setSaveOk, setSaveErrors, deleteTarget.current);
    setDeleteDialogIsOpen(false)
  }

  /**
   * Build list of questions to be passed to Draggable.
   * The Question.Title is unique, so we use this as an identifier
   */
  let questionList = [];
  {questions.map && questions.map((question, index) => {questionList.push(
      <div style={{border: "1px solid white"}} className="pt-1 h-fit overflow-y-hidden">
        <div className="w-6 float-left"><DragIndicatorIcon/></div>
        <div className="w-1/6 float-left pt-1">{question.title}</div>
        <div className="w-4/6 float-left pt-1">{question.heading}</div>
        <div> 
          <EditIcon className="cursor-pointer" onClick={() => router.get(route(editRoute, [objectId, question.id]))}/> 
          <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(question)}/>
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
    const itemDragged = questions[draggedFrom];
    const remainingItems = questions.filter((item, index) => index !== draggedFrom);
    // Rebuild our list splicing in the new item that we copied above.
    setQuestions([
      ...remainingItems.slice(0, draggedTo),
      itemDragged,
      ...remainingItems.slice(draggedTo)
     ]);
     questions.map((question) => console.log(`New Order: ${question.title}`));
     // Don't save it yet, we'll wait til user presses save
     setSaveOk("Unsaved changes");
  }

  /**
   * Navigate to the URL that will allow us to create new questions
   */
  function SaveOrder() {
    questions.map((question) => console.log(`Save Order: ${question.title}`));
    let titleList = [];
    questions.map((question, index) => titleList.push(question.id));
    let newOrder = {
      "newOrder": titleList
    }

    // Save the new order
    SaveAnswersWithId(saveOrderRoute, saveOrderParameters, setSaveOk, setSaveErrors, newOrder);
  }

  return(
    <>
    <DeleteModal open={deleteDialogIsOpen} itemInfo={deleteTarget.current} onConfirm={confirmedDeletion}
      onCancel={() => setDeleteDialogIsOpen(false)} {...props}/>
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route(addRoute, objectId))} children="Add Question" className="mr-4"/>
    <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    <div>
      <div style={{borderBottom: "3px solid white"}}>&nbsp;</div>
      <DraggableList siteConfig={siteConfig} items={questionList} callback={sortCallback}/>          
    </div>    
    <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
      <ThemedButton siteConfig={siteConfig} onClick={SaveOrder} children="Save Question Order" className="mr-4"/>
      <span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span>
    </div>          
    </>
  );
}