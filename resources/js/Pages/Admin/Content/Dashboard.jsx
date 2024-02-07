import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import Admin_RichTextAreaField from '@/Components/Admin/Inputs/Admin.RichTextAreaField';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';

export default function Dashboard(props) {  
  let userAnswers = useRef([]);
  let [errors, setErrors] = useState("");
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
  function saveCallback() {
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
          setErrors([]);
          setSaveOk("Saved Successfully");
        },
        onError: (errors) => {
          console.log("saveAnswers Failed");            
          console.log(errors);
          setErrors(errors);
          setSaveOk(null);
        },
    })
  }
  
  let titleField = { 
    "label" : "Title",
    "placeholder": "",
    "required": true,
    "value": props.dashboard.title
  }

  let titleTextField = { 
    "label": "Title Text",
    "placeholder": "",
    "required": true,
    "value": props.dashboard.titleText
  }

  let submissionField = {
    "label" : "Submission",
    "placeholder": "",
    "required": true,
    "value": props.dashboard.submission
  }

  let inputProps = {handleChange, submitCallBack:saveCallback, errors, siteConfig:props.siteConfig, camalCase:true, sideBySide:true, runInit:true};

  function MyContent() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Add New Control</div>
        <div className="inline-block w-11/12">
          <Admin_TextField field={titleField} {...inputProps}/>
          <Admin_RichTextAreaField field={titleTextField} {...inputProps}/>
          <Admin_TextField field={submissionField} {...inputProps}/>
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/></div>
          <div className="pl-2 font-bold">{saveOk}</div>
        </div>        
      </div>
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
