import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import HelpIcon from '@mui/icons-material/Help';

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
        <div className="p-2 h-20 border-2 border-solid mb-3 rounded-md inline-block w-auto"
          style={{
            backgroundColor: props.siteConfig.theme_admin_help_bg_color,
            textColor: props.siteConfig.theme_admin_help_text_color,
            borderColor: props.siteConfig.theme_admin_content_spacer}}>
          <HelpIcon fontSize="large"/>
            Drag pillars in the list below to re-order them on the main dashboard screen.<br/>
            You will need to save changes. Once saved, new order is published immediately.
        </div>
        <div className="overflow-y-auto w-5/6 mb-2">
          <div>            
          <DraggableList items={props.pillars} callback={sortCallback} siteConfig={props.siteConfig}/>
          </div>
        </div>
        <div id="bottom_menu" className="h-10 pt-2">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveAnswers} children="Update Order"/>
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
