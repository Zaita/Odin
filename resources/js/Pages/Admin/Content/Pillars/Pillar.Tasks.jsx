import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'
import LinkOffIcon from '@mui/icons-material/LinkOff';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import UnlinkModal from '@/Components/Admin/UnlinkModal';
import Admin_DropdownField from '@/Components/Admin/Inputs/Admin.DropdownField';

export default function Pillar_Tasks(props) {  
  let [errors, setErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [linkOk, setLinkOk] = useState(props.saveOk);
  let [linkError, setLinkError] = useState();
  const [dialogIsOpen, setDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let currentTask = useRef({});

  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Task";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId("admin.content.pillar.task.unlink", {id:props.pillar.id, taskId:deleteTarget.current["id"]}, setLinkOk, setLinkError, deleteTarget.current);
    setDialogIsOpen(false)
  }

  function cancelledDeletion() {
    console.log("Deletion Cancelled");
    setDialogIsOpen(false)
  }

  function handleChange(id, value) {
    currentTask.current["task"] = value;
  }

  function saveCallback() {
      SaveAnswersWithId("admin.content.pillar.task.link", props.pillar.id, setLinkOk, setLinkError, currentTask.current);
  }

  let tasksField = {
    "label": "Task",
    "type": "dropdown",
    "value": "",
    "options": props.taskOptions,
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

  function MyContent() {
    return (
      <>
      <UnlinkModal open={dialogIsOpen}
        itemInfo={deleteTarget.current} 
        onConfirm={confirmedDeletion}
        onCancel={cancelledDeletion}
        {...props}/>
      <div>
        <div>
          <div className="font-bold">Link task to pillar</div>
          <div>
            <div className="float-left"><Admin_DropdownField field={tasksField} options={tasksField.options} {...inputProps} runInit/></div>
            <div className="ml-2 mr-2 inline-block"><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Link Task"/></div>  
            <span className="pl-2 font-bold text-green-600">{linkOk}</span>
            <span className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{linkError}</span>          
          </div>
        </div>
        <div className="font-bold mt-5 text-sm">Tasks linked to pillar</div>
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left font-bold">Name</div>
            <div className="w-7/12 float-left font-bold">Type</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.linkedTasks.map((task, index) => {
          return (
            <div key={index} className="pt-1 border-b"
              style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left">{task.name}</div>
            <div className="w-7/12 float-left">{task.type}</div>
            <div> 
              <LinkOffIcon alt="Unlink task from pillar" className="cursor-pointer" onClick={() => openConfirmationModal(task)}/>
            </div>
          </div>)
        })}       
      </div>
      </>
    );
  }

  let topMenuItems = [
    ["Pillar", "admin.content.pillar.edit", props.pillar.id],
    ["Questions", "admin.content.pillar.questions", props.pillar.id],
    ["Tasks", "admin.content.pillar.tasks", props.pillar.id],
  ]

  if (props.pillar?.questionnaire.custom_risks) {
    topMenuItems.push(["Risks", "admin.content.pillar.risks", props.pillar.id])
  }

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Tasks", "admin.content.pillar.tasks", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
