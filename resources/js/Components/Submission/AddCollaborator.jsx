import {React, useState, useRef} from 'react';

import TextField from '../TextField';
import ThemedButton from "../ThemedButton";
import { SaveAnswersWithId } from '../Admin/SaveAnswers';

export default function AddCollaboratorModal(props) {
  let value  = useRef([]);
  let [saveErrors, setSaveErrors] = useState("");  
  const { open, onCancel } = props;
  if (!open) {
      return <></>;
  }

  function handleChange(id, newValue) {
    value.current["email"] = newValue;
  }

  function successCallBack(result) {
    if (result == null) {
      return;
    }
    onCancel();
  }

  function saveAnswersCallback() {
    SaveAnswersWithId("submission.collaborator.add", props.submission.uuid, successCallBack, setSaveErrors, value.current);
  }

  let emailField = { 
    "label" : "Email",
    "placeholder": "bob@example.com",
    "required": true,
    "value": value.current["email"],
  }

  return (
      <div className="fixed inset-0 z-50 overflow-auto bg-smoke-light flex" style={{backgroundColor: "rgb(0, 0, 0, 0.5)"}}>
          <div className="relative p-4 bg-white w-full max-w-md m-auto flex-col flex rounded-lg h-48">
            <div className="w-full">
              Please enter the email address of the person you wish to add as a collaborator:
            </div>
            <div className="w-full">
              <TextField field={emailField} value={emailField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="65px" camalCase runInit/>
            </div>
            <div className="w-full">
            <span className="absolute bottom-0 right-0 p-2">
            <span className="pr-2">
              <ThemedButton siteConfig={props.siteConfig} onClick={onCancel} children="Cancel"/>                  
              <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Add" autofocus/></span>              
            </span>
            </div>
          </div>
      </div>
  );
}