import React, { useRef, useState, Component } from 'react';
import UserLayout from '@/Layouts/UserLayout';

export default function SecurityCatalogue_Control_Edit(props) {  
  let control = props.control;

  function KeyControlBox() {
    return <></>
  }

  function Content() {
    return (
      <div id="inner_content" className="pt-4 pb-4">
        <KeyControlBox/>
        <div className="font-bold text-lg">Description</div>
        <div dangerouslySetInnerHTML={{__html: control.description}}
          className="pt-2 pb-2 mb-2 whitespace-pre-wrap"
          style={{borderBottom: "1px solid " + props.siteConfig.theme_content_bg_color}}/>
        
        <div className="font-bold text-lg">How to implement this control</div>
        <div dangerouslySetInnerHTML={{__html: control.implementation_guidance}}
          className="pt-2 pb-2 mb-2 whitespace-pre-wrap"
          style={{borderBottom: "1px solid " + props.siteConfig.theme_content_bg_color}}/>

        <div className="font-bold text-lg">Expected evidence of implementation</div>
        <div dangerouslySetInnerHTML={{__html: control.implementation_evidence}}
          className="pt-2 pb-2 mb-2 whitespace-pre-wrap"
          style={{borderBottom: "1px solid " + props.siteConfig.theme_content_bg_color}}/>    
      </div>
    );
  }

  let breadcrumb = [
    ["Home", "home"],
    ["Security Controls", "controls"], 
    [props.control.name, "control.view", {id:props.control.id}]
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Security Controls" subheaderText={control.name} 
      breadcrumb={breadcrumb}
      content={<Content/>} />
  );
}
