import React from 'react';
import { router } from '@inertiajs/react'
import VisibilityIcon from '@mui/icons-material/Visibility';

import UserLayout from '@/Layouts/UserLayout';

export default function SecurityControls(props) {
  function Content() {
    return (
      <div id="inner_content">
        <div><span className="font-bold">Security Catalogue:</span>{props.catalogue.name}</div>
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_bg_color,
            backgroundColor: props.siteConfig.theme_content_bg_color
          }}>
            <div className="w-4/12 float-left font-bold whitespace-pre-wrap">Control Name</div>
            <div className="w-7/12 float-left font-bold whitespace-pre-wrap">Description</div>
            <div className="font-bold">Actions</div>
        </div>
        {props.controls?.map((control, index) => {          
          return (
          <div key={index} className="pt-1 border-b"
            style={{borderColor: props.siteConfig.theme_bg_color,
              backgroundColor: props.siteConfig.theme_content_bg_color
            }}>
            <div className="w-4/12 float-left">{control.name}</div>
            <div className="w-7/12 float-left">{control.description ? control.description.substring(0, 100).replaceAll("<p>", "").replaceAll("</p>", "") : "="}</div>
            <div><VisibilityIcon className="cursor-pointer" onClick={() => router.get(route('control.view', [control.id]))}/></div>
          </div>)
        })}    
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Security Controls", "controls"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Security Controls" subheaderText="Security Controls" 
      breadcrumb={breadcrumb}
      content={<Content/>} />
  );
}
