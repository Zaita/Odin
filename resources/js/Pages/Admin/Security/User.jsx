import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function User(props) {  
  let [saveErrors, setSaveErrors] = useState(props.errors ? props.errors : null);
  let [saveOk, setSaveOk] = useState(null);
  const [dialogIsOpen, setDialogIsOpen] = useState(false);

  let deleteTarget = useRef({});

  function openConfirmationModal(user) {
    deleteTarget.current["id"] = user.id;
    deleteTarget.current["type"] = "User";
    deleteTarget.current["name"] = `${user.name} (${user.email})`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswers("admin.security.users.delete", setSaveOk, setSaveErrors, deleteTarget.current);
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
            <div className="w-1/6 float-left font-bold">User Name</div>
            <div className="w-1/6 float-left font-bold">Email</div>
            <div className="w-6/12 float-left font-bold">Groups</div> 
            <div className="font-bold">Actions</div>
        </div>
        {props.users && props.users.data.map && props.users.data.map((user, index) => {
          return (
          <div key={index} style={{borderBottom: "1px solid white"}} className="pt-1">
            <div className="w-1/6 float-left">{user.name}</div>
            <div className="w-1/6 float-left">{user.email}</div>
            <div className="w-6/12 float-left inline-block">{user.groups_string}</div>
            <div> 
              <EditIcon onClick={() => router.get(route('admin.security.users.edit', [user.id]))}/> 
              <DeleteForeverIcon onClick={() => openConfirmationModal(user)}/>
            </div>
          </div>)
        })}       
      </div> 
      <div id="pagination_navbar" className="text-center pt-2" >
      <div className="float-left" style={{fontSize: "11px"}}>Displaying users {props.users.from} to {props.users.to} of {props.users.total}</div>
      {props.users.links.map((link, index) => <a key={index} href={link.url}><span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/></a>)}     
      </div> 
      </>
    );
  }

  let topMenuItems = []
  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.security.users.add'))} children="Add User"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>,
  ];
    

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={actionMenuItems} content={<MyContent props/>}/>
  );
}
