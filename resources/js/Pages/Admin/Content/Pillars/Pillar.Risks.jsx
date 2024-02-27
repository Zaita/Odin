import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import DeleteModal from '@/Components/Admin/DeleteModal';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import { BorderBottom } from '@mui/icons-material';

export default function Pillar_Risks(props) {  
  let [errors, setErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [linkOk, setLinkOk] = useState(props.saveOk);
  let [linkError, setLinkError] = useState();
  const [dialogIsOpen, setDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let currentTask = useRef({});

  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Risk";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId("admin.content.pillar.risk.delete", {id:props.pillar.id, riskId:deleteTarget.current["id"]}, setLinkOk, setLinkError, deleteTarget.current);
    setDialogIsOpen(false)
  }

  function cancelledDeletion() {
    console.log("Deletion Cancelled");
    setDialogIsOpen(false)
  }

  function handleChange(id, value) {
    currentTask.current[id] = value;
  }

  function saveCallback() {
      SaveAnswersWithId("admin.content.pillar.risk.create", props.pillar.id, setLinkOk, setLinkError, currentTask.current);
  }

  let riskName = {
    "label": "Name",
    "type": "text",
  }

  let riskDescription = {
    "label": "Description",
    "type": "text",
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true, runInit:true}

  function MyContent() {
    return (
      <>
      <DeleteModal open={dialogIsOpen}
        itemInfo={deleteTarget.current} 
        onConfirm={confirmedDeletion}
        onCancel={cancelledDeletion}
        {...props}/>
      <div>
        <div style={{BorderBottom: "1px solid " + props.siteConfig.theme_login_bg_color}}>
          <div className="font-bold">Add custom risk to pillar</div>
          <div>
            <div><Admin_TextField field={riskName} {...inputProps}/></div>
            <div><Admin_TextField field={riskDescription} {...inputProps}/></div>
            <div>
              <div className="ml-2 mr-2 inline-block"><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Add Risk"/></div>  
              <span className="pl-2 font-bold text-green-600">{linkOk}</span>
              <span className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{linkError}</span>          
            </div>
          </div>
        </div>
        <div className="font-bold mt-5 text-sm">Customs risks on this pillar</div>
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left font-bold">Name</div>
            <div className="w-7/12 float-left font-bold">Description</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.risks.map((risk, index) => {
          return (
            <div key={index} className="pt-1 border-b"
              style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left">{risk.name}</div>
            <div className="w-7/12 float-left">{risk.description ? risk.description : '-'}</div>
            <div> 
              <DeleteForeverIcon alt="delete risk from pillar" className="cursor-pointer" onClick={() => openConfirmationModal(risk)}/>
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
    ["Risks", "admin.content.pillar.risks", props.pillar.id],
  ]

  let breadcrumb = [
    ["Pillars", "admin.content.pillars"],
    [props.pillar.name, "admin.content.pillar.edit", props.pillar.id],
    ["Risks", "admin.content.pillar.risks", props.pillar.id] 
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
