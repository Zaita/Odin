import React, {useState, useRef, createRef} from 'react';
import UserLayout from '@/Layouts/UserLayout';

import ThemedButton from '@/Components/ThemedButton';
import DSRA_RiskTable from '@/Components/DSRA/RiskTable';
import DSRA_ControlList from '@/Components/DSRA/ControlList';

export default function Submission_Task_DSRA(props) {
  const ref = useRef(null);
  const controls = useRef(props.controls);

  function callback(controlId, newStatus) {
    console.log("Updating controlId: " + controlId + " to a new status: " + newStatus);
    const token = document.head.querySelector('meta[name="csrf-token"]').content;
    const requestOptions = {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token 
      },      
      body: JSON.stringify({ status: newStatus })
    };
    fetch(route("submission.dsra.control.update", controlId), requestOptions)
        .then(response => response.json())
        .then(data => { 
          console.log("Control " + data.name + " (" + data.id + ") implementation status updated to " + data.implementation_status);    
          ref.current.updateTable(data.id, data.implementation_status);
        });
  }

  function Content() {
    return <div id="inner_content">
      <div className="font-bold mb-4">Your risk assessment results</div>
      <div className="mb-4">
        The Digital Security Risk Assessment (DSRA) is developed specifically for your target system. It displays the security risks assuming no controls are in place. It 
        also flags any required treatments that will reduce the target systems risk environment to an acceptable level.
      </div>
      <DSRA_RiskTable {...props} ref={ref}/>
      <div className="mt-4">
        <div className="font-bold mb-4">Recommended Controls</div>
        <div className="mb-4">Start selecting the controls you want to implement to reduce your risk to an acceptable level by clicking on the required treatments</div>        
      </div>
      <div className="pb-5">
        <DSRA_ControlList {...props} controls={props.controls} callback={callback}/>
      </div>
    </div>
  }

  let productName = props.submission.product_name ? props.submission.product_name : "-";

  let breadcrumb = [
    ["Home", "home"],    
    ["Submissions", "submissions"],
    [productName, "submission.submitted", props.submission.uuid],
    [props.task.name, "submission.task.submitted", props.task.uuid]
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText={props.task.name}
      breadcrumb={breadcrumb}
      content={<Content {...props} />}/>
    );
}

