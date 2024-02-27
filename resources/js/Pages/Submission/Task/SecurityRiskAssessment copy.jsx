import React, {useState, useRef} from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';
import DSRA_RiskTable from '@/Components/DSRA/RiskTable';
import DSRA_ControlList from '@/Components/DSRA/ControlList';

export default function Submission_Task_DSRA(props) {
  let [controls, setControls] = useState(props.controls); 



  
  function makeControl(count, status) {
    for (let index = 0; index < count; index++) {
      controls.push({
        "id" : controls.length.toString(),
        "content" : "Control " + (controls.length + index).toString(),
        "sort_order" : controls.length + index,
        "status" : status,
      });    
    }
  }

  makeControl(3, "not_applicable");
  makeControl(6, "not_implemented");
  makeControl(2, "planned");
  makeControl(0, "implemented");
  

  function Content() {
    return <div id="inner_content">
      <div className="font-bold">Your risk assessment results</div>
      <div>
        The Digital Security Risk Assessment (DSRA) is developed specifically for your target system. It displays the security risks assuming no controls are in place. It 
        also flags any required treatments that will reduce the target systems risk environment to an acceptable level.
      </div>
      <DSRA_RiskTable {...props}/>
      <div>
        <div className="font-bold">Recommended Controls</div>
        <div>Start selecting the controls you want to implement to reduce your risk to an acceptable level by clicking on the required treatments</div>        
        <div>
          <div className="w-1/4 inline-block">Key Words Input Box</div>
          <div className="w-1/4 inline-block">Risk Category</div>
          <div className="w-1/4 inline-block">Sort By</div>
          <div className="w-1/4 inline-block">Show Not Applicable</div>

        </div>
      </div>
      <div className="pb-5">
        <div><DSRA_ControlList {...props} controls={controls}/></div>
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

