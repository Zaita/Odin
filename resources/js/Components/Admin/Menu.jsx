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

function ParentMenu({name, url, icon}) {
  return (
    <div className="pl-2">{icon}<span className="pl-1">{name}</span></div>
  );  
}

function SubMenu({name, url}) {
  return (
    <div className={route().current().startsWith(url) ? "pl-4 bg-basedarkred" : "pl-4"}><Link href={route(url)}><SubdirectoryArrowRightIcon/><span className="pl-1">{name}</span></Link></div>
  );
}

export default function Menu({ user, siteConfig}) {
    return (
        <div className="bg-basedarkred h-auto overflow-hidden w-48 text-white float-left">          
          <div className="text-center align-middle border-b-2 border-basedarkdarkred pt-4 pb-4"><Link href="/">{siteConfig.title}</Link></div>
          <div className="text-center align-middle pt-1 pb-1 border-b-2 border-basedarkdarkred"><PersonIcon/><span className="pl-2 pr-5">{user.name}</span><LogoutIcon/></div>
          <div className="bg-basered text-white pt-1" id="admin_menu">  
            <ParentMenu name="Home" url="admin.home" icon={<HomeIcon/>}/>
            <SubMenu name="Reports" url="admin.home.reports"/> 
            <SubMenu name="Audit Log" url="admin.home.auditlog"/> 
            <SubMenu name="Jobs" url="admin.home.jobs"/>
            <ParentMenu name="Security" url="admin.security" icon={<SecurityIcon/>}/>
            <SubMenu name="Users" url="admin.security.users"/>
            <SubMenu name="Groups" url="admin.security.groups"/>
            <ParentMenu name="Content" url="admin.content" icon={<DashboardIcon/>}/>
            <SubMenu name="Dashboard" url="admin.content.dashboard"/>
            <SubMenu name="Pillars" url="admin.content.pillars"/>
            <SubMenu name="Tasks" url="admin.content.tasks"/>
            <SubMenu name="Security Controls" url="admin.content.securitycontrols"/>
            <ParentMenu name="Submissions" url="admin.submissions" icon={<GradingIcon/>}/>
            <SubMenu name="Overview" url="admin.submissions.overview"/>
            <SubMenu name="Lifecycle" url="admin.submissions.lifecycle"/>
            <SubMenu name="Tasks" url="admin.submissions.tasks"/>
            <ParentMenu name="Service Inventory" url="admin.services" icon={<DnsIcon/>}/>
            <SubMenu name="Accreditations" url="admin.services.accreditations"/>
            <ParentMenu name="Configuration" url="admin.configuration" icon={<EngineeringIcon/>}/>
            <SubMenu name="Site Settings" url="admin.configuration.siteconfig"/>
            <SubMenu name="Email" url="admin.configuration.email"/>
            <SubMenu name="Risks" url="admin.configuration.risks"/>
            <SubMenu name="Single Sign-On" url="admin.configuration.sso"/>
          </div>
        </div>
    );
}
