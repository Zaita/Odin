import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function UserAdd(props) {  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "Full name",
    "required": true,
    "value": userAnswers.current["name"],
  }

  let emailField = { 
    "label": "Email",
    "placeholder": "name@example.com",
    "required": true,
    "value": userAnswers.current["email"],
  }

  let passwordField = { 
    "label": "Password",
    "placeholder": "****",
    "required": true,
    "value": userAnswers.current["password"],
  }

  function saveAnswersCallback() {
    SaveAnswers("admin.security.users.create", setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Name */}
          <div className="w-full">
          <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
          </div>
          {/* Email Text */}
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
      <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/>
        <p>{saveOk}</p>
      </div> 
      </>
    );
  }

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} content={<MyContent props/>}/>
  );
}
