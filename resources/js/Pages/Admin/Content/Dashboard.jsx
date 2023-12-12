import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'

import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import RichTextAreaField from '@/Components/RichTextAreaField';
import ThemedButton from '@/Components/ThemedButton';

export default function Dashboard(props) {  
  let userAnswers = useRef([]);
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);

  /**
   * Store the values from our field in the answers:useRef() so we can
   * access it for submitting to the backend on each question save.
   * @param {Field id} id 
   * @param {Field value} value 
   */
  function handleChange(id, value) {
    userAnswers.current[id] = value;
  };

  /**
   * Save the answers. This is called when a question in our submission is completed.
   * We'll send the details to the back end so we can save progress as the user
   * works through their submission
   * @param {Function to call on success} successCallback 
   */
  function saveAnswers() {
    console.log(userAnswers.current);
    // console.log(data);
    router.visit(route('admin.content.dashboard.update'), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        data: {
          ...userAnswers.current
        },
        onSuccess: (page) => {
          console.log("Saved Successfully");
          userAnswers.current = []; // clear these for next form
          setSaveErrors([]);
          setSaveOk("Saved Successfully");
        },
        onError: (errors) => {
          console.log("saveAnswers Failed");            
          console.log(errors);
          setSaveErrors(errors);
          setSaveOk(null);
        },
    })
  }
  
  let titleField = { 
    "label" : "Title",
    "placeholder": "",
    "required": true,
  }

  let titleTextField = { 
    "label": "Title Text",
    "placeholder": "",
    "required": true,
  }

  let submissionField = {
    "label" : "Submission",
    "placeholder": "",
    "required": true,
  }

  const editorRef = useRef(null);
  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Title */}
          <div className="w-full">
          <TextField field={titleField} value={props.dashboard.title} submitCallback={saveAnswers}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>
          {/* Title Text */}
          <div className="w-full">
          <RichTextAreaField field={titleTextField} value={props.dashboard.titleText} submitCallback={saveAnswers}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>            
          {/* Submission */} 
          <div className="w-full">  
          <TextField field={submissionField} value={props.dashboard.submission} submitCallback={saveAnswers}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>            
        </div>
      </div>
      <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswers} children="Save"/>
        <p>{saveOk}</p>
      </div>  
      </>
    );
  }

  let topMenuItems = [
    [ "Dashboard", "admin.content.dashboard"],
    [ "Pillars", "admin.content.dashboard.pillars"],
    [ "Tasks", "admin.content.dashboard.tasks"]
  ]

  let breadcrumb = [
    ["Dashboard", "admin.content.dashboard"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
