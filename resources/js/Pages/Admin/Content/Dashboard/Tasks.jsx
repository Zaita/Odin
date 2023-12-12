import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import { useForm } from '@inertiajs/react'
import PrimaryButton from '@/Components/PrimaryButton';
import { Transition } from '@headlessui/react';

import AdminPanel from '@/Layouts/AdminPanel';
import DraggableList from "@/Components/DraggableList";

let sortedArray = []
  
let sortCallback = async (event, sortedList) => {
 sortedArray = []
 sortedList.forEach(function(item, index) {
   sortedArray.push(item.id);
 });

 console.log(sortedArray);
}

export default function Dashboard({ auth, pillars, siteConfig }) {   
  const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
    sortedOrderOfPillars: '',
  })
  
  function submit(e) {
  e.preventDefault();
  // setData(sortedArray);
  // router.post(route('admin.content.dashboard.reorder'));
  router.post(route('admin.content.dashboard.reorder'), {"newOrder" :sortedArray} );
  }
  
  function MyContent({pillars}) {
    return (
      <div className="flex pl-2">
        Tasks
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
    <AdminPanel auth={auth} siteConfig={siteConfig} topMenuItems={topMenuItems} breadcrumb={breadcrumb} content={<MyContent pillars={pillars}/>}/>
  );
}
