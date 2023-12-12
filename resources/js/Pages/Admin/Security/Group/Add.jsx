import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import { SaveAnswers } from '@/Components/Admin/SaveAnswers';

export default function GroupAdd(props) {  
  let [saveErrors, setSaveErrors] = useState("");
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
    "placeholder": "",
    "required": false,
  }

  function saveAnswersCallback() {
    SaveAnswers("admin.security.groups.create", setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Title */}
          <div className="w-full">
          <TextField field={nameField} value="" submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
          </div>
          {/* Description */}
          <div className="w-full">
          <TextAreaField field={descriptionField} value="" submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase/>
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
