import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import EditIcon from '@mui/icons-material/Edit';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import DeleteModal from '@/Components/Admin/DeleteModal';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_DropdownField from '@/Components/Admin/Inputs/Admin.DropdownField';

export default function User_Groups(props) {  
  let [errors, setErrors] = useState(null);
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [linkOk, setLinkOk] = useState(props.saveOk);
  let [linkError, setLinkError] = useState();
  const [dialogIsOpen, setDialogIsOpen] = useState(false);
  let deleteTarget = useRef({});
  let currentTask = useRef({});


  function openConfirmationModal(user) {
    deleteTarget.current["id"] = user.id;
    deleteTarget.current["type"] = "group";
    setDialogIsOpen(true);
  }

  function confirmedDeletion() {
    console.log("Deletion Confirmed");
    SaveAnswersWithId("admin.security.user.group.unlink", props.user.id, setSaveOk, setErrors, deleteTarget.current);
    setDialogIsOpen(false)
  }

  function cancelledDeletion() {
    console.log("Deletion Cancelled");
    setDialogIsOpen(false)
  }

  function handleChange(id, value) {
    currentTask.current["group"] = value;
  }

  function saveCallback() {
    SaveAnswersWithId("admin.security.user.group.link", props.user.id, setLinkOk, setLinkError, currentTask.current);
  }

  function MyContent() {
    let groupsField = {
      "label": "Groups",
      "type": "dropdown",
      "value": "",
      "options": props.groupOptions,
    }

    let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

    return (
      <>
      <DeleteModal open={dialogIsOpen}
        itemInfo={deleteTarget.current} 
        onConfirm={confirmedDeletion}
        onCancel={cancelledDeletion}
        {...props}/>
      <div>
        <div className="font-bold">Link task to pillar</div>
        <div>
          <div className="float-left"><Admin_DropdownField field={groupsField} options={groupsField.options} {...inputProps} runInit/></div>
          <div className="ml-2 mr-2 inline-block"><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Add To Group"/></div>  
          <span className="pl-2 font-bold text-green-600">{linkOk}</span>
          <span className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{linkError}</span>          
        </div>
      </div>        
      <div>
        <div><span className="text-green-900 font-bold">{saveOk}</span><span className="text-red-900 font-bold">{errors}</span></div>
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left font-bold">Group Name</div>
            <div className="w-3/6 float-left font-bold">Description</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.user.group_membership && props.user.group_membership.map && props.user.group_membership.map((group, index) => {
          return (
          <div key={index} className="pt-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-2/6 float-left">{group.name}</div>
            <div className="w-3/6 float-left">{group.description ? group.description : "-"}</div>
            <div> 
              <DeleteForeverIcon onClick={() => openConfirmationModal(group)}/>
            </div>
          </div>)
        })}       
      </div>  
      </>
    );
  }

  let actionMenuItems = [
    // <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.security.user.add'))} children="Add User"/>,
  ];

  let breadcrumb = [
    ["Users", "admin.security.users"],
    [props.user.name, "admin.security.user.edit", props.user.id],
    ["Groups", "admin.security.user.groups", props.user.id]
  ]

  let topMenuItems = [
    ["User", "admin.security.user.edit", props.user.id],
    ["Groups", "admin.security.user.groups", props.user.id]
  ]

  return (
    <AdminPanel {...props} breadcrumb={breadcrumb} topMenuItems={topMenuItems} actionMenuItems={actionMenuItems} content={<MyContent props/>}/>
  );
}
