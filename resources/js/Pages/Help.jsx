import { useState } from 'react';

import ExpandLessIcon from '@mui/icons-material/ExpandLess';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';

import UserLayout from '@/Layouts/UserLayout';

// Help item object
function HelpItem({item, siteConfig}) {
  let [showContent, setShowContent] = useState(false);

  return (
    <div className="mt-6">
      <div className="p-2 mb-1 align-top"
        style={{backgroundColor: siteConfig.theme_content_bg_color}}>
        <div className="w-10/12 inline-block">
          <div className="font-bold mb-1">{item.name}</div>
          <div className="pl-1">{item.summary}</div>
        </div>
        <div className="inline-block float-right">
          {!showContent && <ExpandMoreIcon onClick={() => setShowContent(true)}/>}
          {showContent && <ExpandLessIcon onClick={() => setShowContent(false)}/>}
        </div>            
      </div>
      <div className="p-1"
        style={{
          display: showContent ? "block" : "none",
          backgroundColor: siteConfig.theme_content_bg_color
        }}>
        {item.content}
      </div>
    </div>
  )
}

export default function Help(props) {
  function Content() {
    return (
      <div id="inner_content" className="mt-5 mb-5">
        {props.items.map((item, index) => <span key={index}><HelpItem item={item} siteConfig={props.siteConfig}/></span>)}
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
      content={<Content/>}
    />
  );
}
