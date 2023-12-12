import React from 'react';
import { Link } from '@inertiajs/react';
import UserLayout from '@/Layouts/UserLayout';
import Submissions from './Home/Submissions';

import BugReportIcon from '@mui/icons-material/BugReport';
import SecurityIcon from '@mui/icons-material/Security';
import QuestionAnswerIcon from '@mui/icons-material/QuestionAnswer';
import LightbulbIcon from '@mui/icons-material/Lightbulb';
import CloudDownloadIcon from '@mui/icons-material/CloudDownload';

function SubHeader({siteConfig}) {
  return(
    <div id="homepage-subheader" style={{ backgroundImage: `url(${siteConfig.subHeaderImagePath})`, height: "150px"}}></div>
  );
}

function Content(props) {
  return(
    <>
    <SubHeader siteConfig={props.siteConfig}/>
    <div style={{backgroundColor: props.siteConfig.themeBgColor, color: props.siteConfig.themeTextColor, minHeight: "600px"}} className="pt-5 items-center">
      <div id="title-box" className="w-3/5 text-xl font-extrabold mb-4 text-left ml-auto mr-auto">{props.dashboard.title}</div>
      <div id="sdlt-description" className="w-3/5 text-xs ml-auto mr-auto" dangerouslySetInnerHTML={{__html: props.dashboard.titleText}} />
      <div id="create-text" className="w-3/5 text-m font-extrabold mb-4 text-left ml-auto mr-auto pt-5">{props.dashboard.submission}</div>
      <div id="pillars" style={{marginBottom: "40px", maxWidth: "1600px"}} className="ml-auto mr-auto">
        <div id="pillar-row" style={{marginTop: "20px", justifyContent: "space-evenly"}} className="flex">
        <PillarList siteConfig={props.siteConfig} pillars={props.pillars}/>
        </div>  
      </div> 
      <Submissions latestSubmissions={props.latestSubmissions}/>
    </div>
    </>
  );
}

class PillarList extends React.Component {
  sortedList;

  constructor(props) {
    super(props);   
    
    this.sortedList = props.pillars.sort((a, b) => a.sort_order - b.sort_order);
  }
 
  icon(icon) {
    switch(icon) {
      case 'shield' : return <SecurityIcon style={{marginBottom: "28px"}} className="justify-center flex align-middle"/>;
      case "message" : return <QuestionAnswerIcon style={{marginBottom: "28px"}} className="justify-center flex align-middle"/>;
      case 'lightbulb' : return <LightbulbIcon style={{marginBottom: "28px"}} className="justify-center flex align-middle"/>;
      case 'download' : return <CloudDownloadIcon style={{marginBottom: "28px"}} className="justify-center flex align-middle"/>;
      default: return <BugReportIcon style={{marginBottom: "28px"}} className="justify-center flex align-middle"/>;
    }
  }

  render() {
    return (
    <>
      {this.sortedList.map((p, index) => 
        <Link key={index} href={"/start/" + p.id} className="inline bg-white" style={{width: "170px", height: "170px", lineHeight: "13px", boxShadow: "0 4px 10px rgba(0,63,100,.12)"}}>
          <div style={{color: this.props.siteConfig.themeHeaderColor, marginTop: "29px", fontSize: "13px", fontWeight: "900"}}  className="text-center">{p.name}</div>
          <div style={{color: this.props.siteConfig.themeHeaderColor, marginTop: "25px"}} className="justify-center flex">{this.icon(p.icon)}</div>
          <div style={{color: this.props.siteConfig.themeTextColor, fontSize: "11px", paddingRight: "25px", paddingLeft: "25px"}}
            className="text-center">{p.caption}</div>
        </Link>      
      )}
    </>
    );
  }
}

export default function Home(props) {
  return (
      <UserLayout {...props} showSubheader={false} content={<Content {...props}/>} selectedMenu="Home"/>
  );
}