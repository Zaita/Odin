import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import AddEdit from '@/Components/Admin/AddEdit';

export default function SiteConfiguration(props) {
  let saveRoute = "admin.configuration.settings.theme.save"
  let inputs = [];

  inputs.push({
    "label" : "Login BG Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_login_bg_color
  });
  
  inputs.push({
    "label" : "BG Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_bg_color
  });

  inputs.push({
    "label" : "Text Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_text_color
  });

  inputs.push({
    "label" : "Header Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_header_color
  });

  inputs.push({
    "label" : "Header Text Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_header_text_color
  });

  inputs.push({
    "label" : "Subheader Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_subheader_color
  });

  inputs.push({
    "label" : "Subheader Text Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_subheader_text_color
  });

  inputs.push({
    "label" : "Breadcrumb Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_breadcrumb_color
  });

  inputs.push({
    "label" : "Hyperlink Color",
    "placeholder": "",
    "required": true,
    "type" : "color",
    "value": props.siteConfig.theme_hyperlink_color
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
