import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function GroupAdd(props) {  
  let [errors, setErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "Group name",
    "required": true,
  }

  let descriptionField = { 
    "label": "Description",
    "required": false,
  }

  function saveCallback() {
    SaveAnswers("admin.security.groups.create", setSaveOk, setErrors, userAnswers.current)
  }

  let inputProps = {submitCallback:saveCallback, handleChange, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true}

  function MyContent() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Add New Group</div>
        <div className="inline-block w-11/12">
          <Admin_TextField field={nameField} {...inputProps}/>
          <Admin_TextField field={descriptionField}  {...inputProps}/>
          <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
            <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/></div>
            <div className="pl-2 font-bold">{saveOk}</div>
          </div> 
        </div>
      </div>
    );
  }

  let breadcrumb = [
    ["Groups", "admin.security.groups"]
  ]

  return (
    <AdminPanel {...props} breadcrumb={breadcrumb} topMenuItems={[]} actionMenuItems={[]} content={<MyContent props/>}/>
  );
}
