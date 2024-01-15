import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

import UserLayout from '@/Layouts/UserLayout';

export default function Submissions(props) {
  function Content(props) {
    return (
      <div id="content_box" className="mt-5">
        <div>Title: {props.siteConfig.title} </div>
        <div>footerText: {props.siteConfig.footerText}</div>
        <div>odinEmail: {props.siteConfig.odinEmail}</div>
        <div>securityTeamEmail: {props.siteConfig.securityTeamEmail}</div>
        <div>logoPath: {props.siteConfig.logoPath}</div>
        <div>subHeaderImagePath: {props.siteConfig.subHeaderImagePath}</div>
        <div id="colour_palette" className="bg-pink-800">
          <div>Theme BG Colour: <span style={{color: props.siteConfig.themeBgColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Text Colour: <span style={{color: props.siteConfig.themeTextColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Header Colour: <span style={{color: props.siteConfig.themeHeaderColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Header Text Colour: <span style={{color: props.siteConfig.themeHeaderTextColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Subheader Colour: <span style={{color: props.siteConfig.themeSubheaderColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Breadcrumb Colour: <span style={{color: props.siteConfig.themeBreadcrumbColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Hyperlink Colour: <span style={{color: props.siteConfig.themeHyperlinkColor}}>XXXXXXXXXXX</span></div>
          <div>Theme Text Colour: <span style={{color: props.siteConfig.themeTextColor}}>XXXXXXXXXXX</span></div>
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
