import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';
import {Link} from '@inertiajs/react';

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
    deleteTarget.current["type"] = "Security Catalogue";
    deleteTarget.current["name"] = `${target.name}`;
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswers("admin.content.securitycatalogue.delete", setSaveOk, setSaveErrors, deleteTarget.current);
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
            <div className="w-2/12 float-left font-bold">Catalogue Name</div>
            <div className="w-9/12 float-left font-bold">Description</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.catalogues?.map((catalogue, index) => {          
          return (
          <div key={index} className="pt-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/12 float-left">{catalogue.name}</div>
            <div className="w-9/12 float-left">{catalogue.description ? catalogue.description : "-"}</div>
            <div> 
              <EditIcon className="cursor-pointer" onClick={() => router.get(route('admin.content.securitycatalogue.edit', [catalogue.id]))}/> 
              <DeleteForeverIcon className="cursor-pointer" onClick={() => openConfirmationModal(catalogue)}/>
              <a href={route('admin.content.securitycatalogue.download', catalogue.id)}>
                <DownloadIcon className="cursor-pointer"/>
              </a>
            </div>
          </div>)
        })}       
      </div> 
      </>
    );
  }

  let actionMenuItems = [
    <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.content.securitycatalogue.add'))} children="Add Catalogue"/>,
    <><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{saveErrors}</span></>
  ];

  let breadcrumb = [
    ["Security Controls", "admin.content.securitycatalogues"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
