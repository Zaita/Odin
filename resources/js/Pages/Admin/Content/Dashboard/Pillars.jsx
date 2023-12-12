import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'

import AdminPanel from '@/Layouts/AdminPanel';
import DraggableList from "@/Components/DraggableList";
import ThemedButton from '@/Components/ThemedButton';

let sortedArray = []
  
let sortCallback = async (event, sortedList) => {
 sortedArray = []
 sortedList.forEach(function(item, index) {
   sortedArray.push(item.id);
 });

 console.log(sortedArray);
}

export default function Dashboard(props) {   
  let [saveOk, setSaveOk] = useState(null);

  /**
   * Save the answers. This is called when a question in our submission is completed.
   * We'll send the details to the back end so we can save progress as the user
   * works through their submission
   * @param {Function to call on success} successCallback 
   */
  function saveAnswers() {
    // console.log(data);
    router.visit(route('admin.content.dashboard.pillars.updateorder'), {
        method: "post",
        preserveScroll: true,
        preserveState: true,
        data: {
          "newOrder" :sortedArray
        },
        onSuccess: (page) => {
          console.log("Saved Successfully");
          setSaveOk("Saved Successfully");
        },
        onError: (errors) => {
          console.log("saveAnswers Failed");            
          console.log(errors);
          setSaveOk("Save Failed");
        },
    })
  }
  
  function MyContent() {
    return (
    <>
      <div>
        <div className="pl-2 h-10 align-middle border-2 border-white border-solid mb-3">
            Drag pillars to re-order them on the dashboard:
        </div>
        <div className="overflow-y-auto w-5/6 mb-2">
          <div>            
          <DraggableList items={props.pillars} callback={sortCallback}/>
          </div>
        </div>
        <div id="bottom_menu" className="h-10 border-t-2 border-solid border-white pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswers} children="Save"/>
        <p>{saveOk}</p>
        </div>
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
    <AdminPanel {...props} topMenuItems={topMenuItems} breadcrumb={breadcrumb} content={<MyContent/>}/>
  );
}
