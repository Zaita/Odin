import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';
import {Link} from '@inertiajs/react';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function SecurityCatalogue_Controls(props) {  
  let [saveErrors, setSaveErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(null);
  const [dialogIsOpen, setDialogIsOpen] = useState(false);

  let deleteTarget = useRef({});

  function openConfirmationModal(target) {
    deleteTarget.current["id"] = target.id;
    deleteTarget.current["type"] = "Security Control";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId("admin.content.securitycontrol.delete", {id:props.catalogue.id, controlId:deleteTarget.current["id"]}, setSaveOk, setSaveErrors, deleteTarget.current);
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
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/12 float-left font-bold">Control Name</div>
            <div className="w-6/12 float-left font-bold">Description</div>
            <div className="w-3/12 float-left font-bold">Reference Standards</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.controls?.map((control, index) => {          
          return (
          <div key={index} className="pt-1 border-b overflow-x-auto"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/12 float-left whitespace-pre-wrap">{control.name}</div>
            <div className="w-6/12 float-left whitespace-pre-wrap">{control.description ? control.description.substring(0, 200).replaceAll("<p>", "").replaceAll("</p>", "") : "="}</div>
            <div className="w-3/12 float-left whitespace-pre-wrap">{control.reference_standards ? control.reference_standards : "-"}</div>
            <div> 
              <EditIcon className="cursor-pointer" onClick={() => router.get(route('admin.content.securitycontrol.edit', {id:props.catalogue.id, controlId:control.id}))}/> 
              <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(control)}/>
              <a href={route('admin.content.securitycontrol.download', control.id)}>
                <DownloadIcon className="cursor-pointer"/>
              </a>
            </div>
          </div>)
        })}       
      </div> 
      </>
    );
  }

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"],
    [props.catalogue.name, "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Security Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  let topMenuItems = [
    ["Catalogue", "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.content.securitycontrol.add', props.catalogue.id))} children="Add Control"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>
  ];

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
