import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function Tasks(props) {  
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  const [dialogIsOpen, setDialogIsOpen] = useState(false);

  let deleteTarget = useRef({});

  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Task";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswers("admin.content.tasks.delete", setSaveOk, setSaveErrors, deleteTarget.current);
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
            <div className="w-2/6 float-left font-bold">Name</div>
            <div className="w-7/12 float-left font-bold">Type</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.tasks?.data?.map((task, index) => {
          let x = task.id;
          return (
          <div key={index} style={{borderBottom: "1px solid white"}} className="pt-1">
            <div className="w-2/6 float-left">{task.name}</div>
            <div className="w-7/12 float-left">{task.type}</div>
            <div> 
              <EditIcon className="cursor-pointer" onClick={() => router.get(route('admin.content.task.edit', [task.id]))}/> 
              <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(task)}/>
              <a href={route('admin.content.task.download', x)}>
                <DownloadIcon className="cursor-pointer"/>
              </a>
            </div>
          </div>)
        })}       
      </div> 
      <div id="pagination_navbar" className="text-center pt-2" >
      <div className="float-left" style={{fontSize: "11px"}}>Displaying tasks {props.tasks.from} to {props.tasks.to} of {props.tasks.total}</div>
      {props.tasks.links.map((link, index) => <a key={index} href={link.url}><span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/></a>)}     
      </div> 
      </>
    );
  }

  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.content.task.add'))} children="Add Task"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>
  ];

  let breadcrumb = [
    ["Tasks", "admin.content.tasks"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
