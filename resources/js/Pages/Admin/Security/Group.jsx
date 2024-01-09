import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function Group(props) {  
  let [saveErrors, setSaveErrors] = useState(props.errors ? props.errors : null);
  let [saveOk, setSaveOk] = useState(null);
  const [dialogIsOpen, setDialogIsOpen] = useState(false);

  let deleteTarget = useRef({});

  function openConfirmationModal(group) {
    deleteTarget.current["id"] = group.id;
    deleteTarget.current["type"] = "Security Group";
    deleteTarget.current["name"] = group.name;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswers("admin.security.groups.delete", setSaveOk, setSaveErrors, deleteTarget.current);
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
            <div className="w-2/6 float-left font-bold">Group Name</div>
            <div className="w-3/6 float-left font-bold">Description</div>
            <div className="w-1/12 float-left font-bold">Users</div> 
            <div className="font-bold">Actions</div>
        </div>
        {props.groups && props.groups.map && props.groups.map((group, index) => {
          return (
          <div key={index} style={{borderBottom: "1px solid white"}} className="pt-1">
            <div className="w-2/6 float-left">{group.name}</div>
            <div className="w-3/6 float-left">{group.description ? group.description : "n/a"}</div>
            <div className="w-1/12 float-left">{group.users}</div>
            <div> 
              <EditIcon onClick={() => router.get(route('admin.security.groups.edit', [group.id]))}/> 
              <DeleteForeverIcon onClick={() => openConfirmationModal(group)}/>
            </div>
          </div>)
        })}       
      </div>  
      </>
    );
  }

  let topMenuItems = []
  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.security.groups.add'))} children="Add Group"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>,
  ];
    

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={actionMenuItems} content={<MyContent props/>}/>
  );
}