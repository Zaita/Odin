import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import AddEdit from '@/Components/Admin/AddEdit';

export default function SiteConfiguration(props) {
  let saveRoute = "admin.configuration.settings.save"
  let inputs = [];

  inputs.push({
    "label" : "Title",
    "placeholder": "",
    "required": true,
    "type" : "text",
    "value": props.siteConfig.title
  });
  
  inputs.push({
    "label" : "Odin Email",
    "placeholder": "",
    "required": true,
    "type" : "email",
    "value": props.siteConfig.odin_email
  });

  inputs.push({
    "label" : "Security Team Email",
    "placeholder": "",
    "required": true,
    "type" : "email",
    "value": props.siteConfig.security_team_email
  });

  inputs.push({
    "label" : "Footer Text",
    "placeholder": "",
    "required": true,
    "type" : "text",
    "value": props.siteConfig.footer_text
  });
  
  let topMenuItems = [
    [ "Global", "admin.configuration.settings"],
    [ "Theme", "admin.configuration.settings.theme"],
    [ "Images", "admin.configuration.settings.images"],
    [ "Alert", "admin.configuration.settings.alert"]
  ];

  return (
    <AdminPanel {...props} actionMenuItems={[]} topMenuItems={topMenuItems} content={<AddEdit {...props} inputs={inputs} saveRoute={saveRoute}/>}/>
  );
}
