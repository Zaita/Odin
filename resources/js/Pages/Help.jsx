import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

import UserLayout from '@/Layouts/UserLayout';

export default function Submissions(props) {
  function Content(props) {
    return (
      <div id="content_box" className="mt-5">
        <div>Title: {props.siteConfig.title} </div>
        <div>footer_text: {props.siteConfig.footer_text}</div>
        <div>odin_email: {props.siteConfig.odin_email}</div>
        <div>security_team_email: {props.siteConfig.security_team_email}</div>
        <div>logo_path: {props.siteConfig.logo_path}</div>
        <div>subheader_image_path: {props.siteConfig.subheader_image_path}</div>
        <div id="colour_palette" className="bg-pink-800">
          <div>Theme BG Colour: <span style={{color: props.siteConfig.theme_bg_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Text Colour: <span style={{color: props.siteConfig.theme_text_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Header Colour: <span style={{color: props.siteConfig.theme_header_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Header Text Colour: <span style={{color: props.siteConfig.theme_header_text_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Subheader Colour: <span style={{color: props.siteConfig.theme_subheader_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Breadcrumb Colour: <span style={{color: props.siteConfig.theme_breadcrumb_color}}>XXXXXXXXXXX</span></div>
          <div>Theme Hyperlink Colour: <span style={{color: props.siteConfig.theme_hyperlink_color}}>XXXXXXXXXXX</span></div>
        </div>
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Help", "help"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Help" subheaderText="Help" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}
