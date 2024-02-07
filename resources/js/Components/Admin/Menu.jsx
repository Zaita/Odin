import { Link } from '@inertiajs/react';
import HomeIcon from '@mui/icons-material/Home';
import PersonIcon from '@mui/icons-material/Person';
import LogoutIcon from '@mui/icons-material/Logout';
import SubdirectoryArrowRightIcon from '@mui/icons-material/SubdirectoryArrowRight';
import SecurityIcon from '@mui/icons-material/Security';
import DashboardIcon from '@mui/icons-material/Dashboard';
import EngineeringIcon from '@mui/icons-material/Engineering';
import GradingIcon from '@mui/icons-material/Grading';
import DnsIcon from '@mui/icons-material/Dns';



export default function Menu({ user, siteConfig}) {
  function getIcon(name) {
    switch(name) {
      case "Home":
        return <HomeIcon/>;      
    }

    return <DnsIcon/>
  }

  function MenuItem({item}) {
    function SubMe() {
      if (route().current().startsWith(item.url))
        return <SubMenu name="Reports" url="admin.home.reports"/>

      return <></>;
    }

    return (
      <>
        <div className="pl-2 text-sm font-bold" style={{color: siteConfig.theme_admin_menu_parent_text_color}}>{getIcon(item.label)}<span className="pl-1">{item.label}</span></div>
        {item.pages.map((page, index) => <SubMe key={index}/>)}
      </>
    )
  }

function ParentMenu({name, url, icon}) {
  let topBox = <div className="text-sm font-bold" style={{color: siteConfig.theme_admin_menu_parent_text_color}}>{icon}<span className="pl-1">{name}</span></div>;
  return (
    <>
    {topBox}
    </>
  );  
}

function SubMenu({name, url}) {
  let newStyle = {backgroundColor: siteConfig.theme_admin_menu_bg_color, color: siteConfig.theme_admin_menu_text_color};
  if (route().current().startsWith(url)) {
    newStyle = {
      backgroundColor: siteConfig.theme_admin_menu_selected_bg_color, 
      color: siteConfig.theme_admin_menu_selected_text_color,
      borderRadius: "5px",
      fontWeight:'bolder',
    };
  }

  return (
    <div className="pl-2" style={{...newStyle}}
    //   style={{backgroundColor: route().current().startsWith(url) ? siteConfig.theme_admin_menu_selected_bg_color : siteConfig.theme_admin_menu_bg_color,
    //   color: route().current().startsWith(url) ? siteConfig.theme_admin_menu_selected_text_color : siteConfig.theme_admin_menu_text_color,
    // borderRadius: "5px" }}
    >
      <Link href={route(url)}><SubdirectoryArrowRightIcon/><span className="pl-1">{name}</span></Link>
    </div>
  );
}

let menus = [
  {
    "label" : "Home",
    "url" : "admin.home",
    "pages" : [
      {
        "label" : "Reports",
        "route" : "admin.home.reports",
      },
      {
        "label" : "Audit Log",
        "route" : "admin.home.auditlog",        
      },
      {
        "label" : "Jobs",
        "route" : "admin.home.jobs"
      }
    ]    
  },
  {
    "label" : "Security",
    "url" : "admin.security",
    "pages" : [
      {
        "label" : "Users",
        "route" : "admin.security.users",
      },
      {
        "label" : "Roles",
        "route" : "admin.security.roles",
      },
      {
        "label" : "Groups",
        "route" : "admin.security.groups"
      }
    ]
  },
];


return (
    <div className="h-auto overflow-hidden w-48 float-left pl-2 pr-2"
      style={{backgroundColor: siteConfig.theme_admin_menu_bg_color, color: siteConfig.theme_admin_menu_text_color}}>          
      <div className="text-center align-middle border-b-2 pt-4 pb-4"
        style={{borderColor: siteConfig.theme_admin_menu_logout_border_color}}>
        <Link href="/">{siteConfig.title}</Link>
      </div>
      <div className="text-center align-middle pt-1 pb-1 border-b-2"
        style={{borderColor: siteConfig.theme_admin_menu_logout_border_color}}>
        <PersonIcon/><span className="pl-2 pr-5">{user.name}</span><LogoutIcon/>
      </div>
      <div className="pt-1" id="admin_menu">
      {/* {menus.map((item, index) => {
        return (<MenuItem item={item} key={index}/>)
      })} */}

        <ParentMenu name="Home" url="admin.home" icon={<HomeIcon/>}/>
        <SubMenu name="Reports" url="admin.home.reports"/> 
        <SubMenu name="Audit Log" url="admin.home.auditlog"/> 
        {/* <SubMenu name="Jobs" url="admin.home.jobs"/> */}
        <ParentMenu name="Security" url="admin.security" icon={<SecurityIcon/>}/>
        <SubMenu name="Users" url="admin.security.users"/>
        <SubMenu name="Groups" url="admin.security.groups"/>
        <ParentMenu name="Content" url="admin.content" icon={<DashboardIcon/>}/>
        <SubMenu name="Dashboard" url="admin.content.dashboard"/>
        <SubMenu name="Pillars" url="admin.content.pillars"/>
        <SubMenu name="Tasks" url="admin.content.tasks"/>
        <SubMenu name="Security Controls" url="admin.content.securitycatalogues"/>
        <ParentMenu name="Records" url="admin.records" icon={<GradingIcon/>}/>
        <SubMenu name="Submissions" url="admin.records.submissions"/>
        <SubMenu name="Tasks" url="admin.records.submissions"/>
        <ParentMenu name="Service Inventory" url="admin.services" icon={<DnsIcon/>}/>
        <SubMenu name="Accreditations" url="admin.services.accreditations"/>
        <ParentMenu name="Configuration" url="admin.configuration" icon={<EngineeringIcon/>}/>
        <SubMenu name="Settings" url="admin.configuration.settings"/>
        <SubMenu name="Email" url="admin.configuration.email"/>
        <SubMenu name="Risks" url="admin.configuration.risks"/>
        <SubMenu name="Single Sign-On" url="admin.configuration.sso"/>
      </div>
    </div>
  );
}
