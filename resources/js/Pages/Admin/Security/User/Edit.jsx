import React, { useRef, useState } from 'react';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function UserEdit(props) {  
  let userAnswers = useRef([]);
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);

  let groupName = useRef([]);
  let [grpSaveErrors, setGrpSaveErrors] = useState("");
  let [grpSaveOk, setGrpSaveOk] = useState(null);

  let nameField = { 
    "label" : "Name",
    "placeholder": "",
    "required": true,
    "value": props.user?.name,
  }

  let emailField = { 
    "label": "Email",
    "placeholder": "",
    "required": true,
    "value": props.user?.email,
  }

  let passwordField = { 
    "label": "Password",
    "placeholder": "****",
    "required": false,
    "value": userAnswers.current["password"],
  }

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  function saveAnswersCallback() {
    userAnswers.current["id"] = props.user.id;    
    SaveAnswers("admin.security.users.save", setSaveOk, setSaveErrors, userAnswers.current)
  }

  // Variables for Adding user to a group
  let groupField = { 
    "label": "Group Name",
    "placeholder": "",
    "required": false,
    "value": userAnswers.current["password"],
  }

  function handleGroupChange(id, value) {
    groupName.current["name"] = value;
  }

  function saveGroupAnswersCallback() {
    groupName.current["user_id"] = props.user.id;    
    SaveAnswers("admin.security.users.groups.add", setGrpSaveOk, setGrpSaveErrors, groupName.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Title */}
          <div className="w-full">
          <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
          </div>
          {/* Email */}
          <div className="w-full">
          <TextField field={emailField} value={emailField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
          </div> 
          {/* Password */}
          <div className="w-full">
          <TextField field={passwordField} value={passwordField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} type="Password" camalCase/>
          </div>           
        </div>
      </div>
      <div id="bottom_menu" className="h-10 pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/>
        <p>{saveOk}</p>
      </div>

      {/* Add Group Form */}
      <div className="mt-4 pt-2 border-t-2 border-solid border-white">
        <div className="flex">
          <div className="overflow-y-auto w-5/6">
            {/* Group Name */}
            <div className="w-full">
            <TextField field={groupField} value={groupField.value} submitCallback={saveGroupAnswersCallback}
                  handleChange={handleGroupChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
            </div>          
          </div>
        </div>  
        <div id="bottom_menu" className="h-10 pt-2">
          <ThemedButton siteConfig={props.siteConfig} onClick={saveGroupAnswersCallback} children="Add To Group"/>
          <p>{grpSaveErrors}</p>
        </div>
      </div>

      {/* Group List */}
      <div className="mt-4 pt-2 border-t-2 border-solid border-white">
        <div>Groups Assigned to {nameField.value}</div>
        <div>
        {props.user.group_membership.map((group, index) => {
          return(
            <div key={index}>
              <DeleteForeverIcon/> {group.name}              
            </div>
          )
        })}
        </div>
      </div>
      </>
    );
  }

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} content={<MyContent props/>}/>
  );
}
