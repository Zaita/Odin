import { useState } from 'react';
import { Link } from '@inertiajs/react';

import Subheader from '@/Components/Questionnaire/Subheader';
import HomeIcon from '@mui/icons-material/Home';
import FormatListBulletedIcon from '@mui/icons-material/FormatListBulleted';
import GppGoodIcon from '@mui/icons-material/GppGood';
import HelpIcon from '@mui/icons-material/Help';
import LogoutIcon from '@mui/icons-material/Logout';

function MenuIcon({content, icon, selectedMenu, siteConfig, url}) {
  return(
    <Link href={url} 
      style={{height: "50px", backgroundColor: selectedMenu == content ? siteConfig.themeSubheaderColor : siteConfig.themeHeaderColor}}
      className="flex flex-row, pl-4 pr-4 justify-center items-center cursor-pointer text-xs">
      <div style={{color: siteConfig.themeHeaderTextColor}}>
        <div style={{height: "18px", width: "18px", marginRight: "5px", marginTop: "-1px", fontSize: "18px"}} className="float-left">{icon}</div>
        {content}
      </div>
    </Link>
  )
}

function Header({user, siteConfig, selectedMenu}) {
  return(
    <div id="header" style={{"height" : "50px", "backgroundColor": siteConfig.themeHeaderColor}} className="flex align-middle items-center">
      <Link href="/">
      <div id="logo" className="text-white"><img src={siteConfig.logoPath} style={{height: "36px", marginLeft: "8px", verticalAlign: "middle"}} alt="Odin Logo"/></div>
      </Link>
      <div className="inline-flex mr-0 ml-auto">
        <span className="text-white pr-2">{user?.email}</span>
        <MenuIcon content="Home" url="/" icon={<HomeIcon fontSize="inherit"/>} selectedMenu={selectedMenu} siteConfig={siteConfig}/>
        <MenuIcon content="Submissions" url="/submissions" icon={<FormatListBulletedIcon fontSize="inherit"/>} selectedMenu={selectedMenu} siteConfig={siteConfig}/>
        <MenuIcon content="Approvals" url="/approvals" icon={<GppGoodIcon fontSize="inherit"/>} selectedMenu={selectedMenu} siteConfig={siteConfig}/>
        <MenuIcon content="Help" url="/help" icon={<HelpIcon fontSize="inherit"/>} selectedMenu={selectedMenu} siteConfig={siteConfig}/>
        <MenuIcon content="Log out" url="/logout" icon={<LogoutIcon fontSize="inherit"/>} selectedMenu={selectedMenu} siteConfig={siteConfig}/>          
      </div>
    </div>
  );
}

function Footer({siteConfig}) {
  return(
    <div id="footer" 
      style={{height: "72px", paddingTop: "25px", backgroundColor: siteConfig.themeHeaderColor, color: siteConfig.themeHeaderTextColor}} 
      className="align-middle items-center text-center text-xs align justify-center">
      {siteConfig.footerText}
    </div>
  );
} 

export default function UserLayout({ user, siteConfig, breadcrumb=[], showSubheader = true, subheaderText="", selectedMenu = "", content = ""}) {
  const [selectedHeaderMenuItem, setSelectedHeaderMenuItem] = useState('Home');

  let subheader = showSubheader ? <Subheader siteConfig={siteConfig} breadcrumb={breadcrumb} text={subheaderText} /> : "";

  return (
    <div id="page" className="text-xs" style={{
        backgroundColor: siteConfig.themeBgColor,
        fontSize: "12px",
        fontFamily: "Roboto",
        fontWeight: "400",
        boxSizing: "border-box",
        lineHeight: "18px",
        }}>
      <Header user={user} siteConfig={siteConfig} selectedMenu={selectedMenu}/>     
      {subheader}
      <div id="content" style={{minHeight: "200px", backgroundColor: siteConfig.themeBgColor}}>
        {content}
      </div>
      <Footer siteConfig={siteConfig}/>
    </div>
  );
}