import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import AddEdit from '@/Components/Admin/AddEdit';

export default function SiteConfiguration(props) {
  let saveRoute = "admin.configuration.settings.theme.save"
  let inputs = [];

  inputs.push({
    "label" : "Alternate hostname for email",
    "placeholder": "",
    "required": true,
    "type" : "text",
    "value": props.siteConfig.alternate_hostname_for_email
  });
  
  inputs.push({
    "label" : "Email from address",
    "placeholder": "",
    "required": true,
    "type" : "email",
    "value": props.siteConfig.email_from_address
  });

  inputs.push({
    "label" : "Email signature",
    "placeholder": "",
    "required": true,
    "type" : "richtexteditor",
    "value": props.siteConfig.email_signature
  });

  let topMenuItems = [
    [ "Main", "admin.configuration.email"],
    [ "Start", "admin.configuration.email.start"],
    [ "Summary", "admin.configuration.email.summary"],
    [ "Submitted", "admin.configuration.email.submitted"],
    [ "All Tasks Complete", "admin.configuration.email.alltaskscomplete"],
    [ "Approval", "admin.configuration.email.approval"],
    [ "Tasks", "admin.configuration.email.tasks"],    
  ];

  return (
    <AdminPanel {...props} actionMenuItems={[]} topMenuItems={topMenuItems} content={<AddEdit {...props} inputs={inputs} saveRoute={saveRoute}/>}/>
  );
}
