import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function Pillars(props) {  
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  const [dialogIsOpen, setDialogIsOpen] = useState(false);

  let deleteTarget = useRef({});

  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Risk";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswers("admin.configuration.risk.delete", setSaveOk, setSaveErrors, deleteTarget.current);
    setDialogIsOpen(false)
  }

  function cancelledDeletion() {
    console.log("Deletion Cancelled");
    setDialogIsOpen(false)
  }

  function MyContent() {
    return (
      <>
      <DeleteModal open={dialogIsOpen}
        itemInfo={deleteTarget.current} 
        onConfirm={confirmedDeletion}
        onCancel={cancelledDeletion}
        {...props}/>
      <div>
        <div style={{borderBottom: "3px solid white"}}>
            <div className="w-1/6 float-left font-bold">Name</div>
            <div className="w-9/12 float-left font-bold">Description</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.risks?.map((object, index) => {
          let x = object.id;
          return (
          <div key={index} style={{borderBottom: "1px solid white"}} className="pt-1">
            <div className="w-1/6 float-left">{object.name}</div>
            <div className="w-9/12 float-left">{object.description}</div>
            <div> 
              <EditIcon className="cursor-pointer" onClick={() => router.get(route('admin.configuration.risk.edit', [object.id]))}/> 
              <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(object)}/>
            </div>
          </div>)
        })}       
      </div> 
      </>
    );
  }

  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.configuration.risk.add'))} children="Add Risk"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>
  ];

  let breadcrumb = [
    ["Risks", "admin.configuration.risks"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
