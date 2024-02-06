import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import AddEdit from '@/Components/Admin/AddEdit';

export default function SiteConfiguration(props) {
  let saveRoute = "admin.configuration.email.start.save"
  let inputs = [];

  inputs.push({
    "label" : "Email Subject",
    "placeholder": "",
    "required": true,
    "type" : "text",
    "value": props.email.subject
  });

  inputs.push({
    "label" : "Content",
    "placeholder": "",
    "required": true,
    "type" : "richtexteditor",
    "value": props.email.content
  });


  let topMenuItems = [
    [ "Main", "admin.configuration.settings.email"],
    [ "Start", "admin.configuration.settings.email.start"],
    [ "Summary", "admin.configuration.settings.email.summary"],
    [ "Submitted", "admin.configuration.settings.email.submitted"],
    [ "All Tasks Complete", "admin.configuration.settings.email.alltaskscomplete"],
    [ "Approval", "admin.configuration.settings.email.approval"],
    [ "Tasks", "admin.configuration.settings.email.tasks"],    
  ];

  return (
    <AdminPanel {...props} actionMenuItems={[]} topMenuItems={topMenuItems} content={<AddEdit {...props} inputs={inputs} saveRoute={saveRoute}/>}/>
  );
}
