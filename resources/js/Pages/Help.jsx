import React from 'react';

import UserLayout from '@/Layouts/UserLayout';
import SubHeader from '@/Components/Home/SubHeader';


function Content({siteConfig, pillars, latestSubmissions}) {
  return(
    <>
    <SubHeader siteConfig={siteConfig}/>
    <div style={{backgroundColor: siteConfig.themeBgColor, color: siteConfig.themeTextColor, minHeight: "600px"}}>
      HELP PAGE!
    </div>
    </>
  );
}

export default function Help({ auth, siteConfig, pillars, latestSubmissions}) {
  return (
    <UserLayout siteConfig={siteConfig} content={<Content siteConfig={siteConfig}/>} selectedMenu="Help"/>
  );
}