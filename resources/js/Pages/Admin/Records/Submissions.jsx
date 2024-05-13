import React from 'react';
import { router } from '@inertiajs/react'
import EditIcon from '@mui/icons-material/Edit';
import DownloadIcon from '@mui/icons-material/Download';
import {Link} from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';

export default function SubmissionsOverview(props) {  
  function getNiceStatus(status) {
    let niceStatus = "";
    switch(status) {
      case "in_progress":
        niceStatus = "In progress";
        break;
      case "in_review":
        niceStatus = "Awaiting submit";
        break;
      case "submitted":
        niceStatus = "Awaiting task completion";
        break;
      case "waiting_for_approval":
          niceStatus = "Waiting for approval";
          break;    
      case "approved":
        niceStatus = "Approved";
        break;      
      case "expired":
        niceStatus = "Expired";
        break;                       
      default:
        niceStatus = "-";
    }
    return niceStatus;
  }

  function MyContent() {
    return (
      <>      
      <div id="submissions" className="mt-5">
        <div id="submission_box">
        <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-1/12 float-left font-bold">Date Created</div>
            <div className="w-2/12 float-left font-bold">Pillar</div>
            <div className="w-3/12 float-left font-bold">Product Name</div>
            <div className="w-2/12 float-left font-bold">Submitter</div> 
            <div className="w-1/12 float-left font-bold">Tasks Completed</div>
            <div className="w-2/12 float-left font-bold">Status</div>
            <div className="font-bold">Actions</div>
        </div>          
          {props.submissions.data.map((submission, index) => (
          <div key={index} className="pt-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-1/12 float-left">{submission.created_at_short}</div>
            <div className="w-2/12 float-left">{submission.pillar_name}</div>
            <div className="w-3/12 float-left">{submission.product_name ? submission.product_name : "-"}</div> 
            <div className="w-2/12 float-left">{submission.submitter_name}</div>
            <div className="w-1/12 float-left">{submission.tasks_completed}</div>
            <div className="w-2/12 float-left">{getNiceStatus(submission.status)}</div>
            <div>
            <Link href={"/admin/records/submission/" + submission.id}><ChevronRightIcon/></Link>
            </div>
          </div>               
          ))}
        </div>
        <div id="pagination_navbar" className="text-center pt-2" >
          <div className="float-left" style={{fontSize: "11px"}}>Displaying entries {props.submissions.from} to {props.submissions.to} of {props.submissions.total}</div>
          {props.submissions.links.map((link, index) => <a key={index} href={link.url}><span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/></a>)}
        </div>
      </div>
      </>
    );
  }

  let topMenuItems = [
    ["In Progress", "admin.records.submissions"],
    ["Waiting for Approval", "admin.records.submissions"],
    ["Approved", "admin.records.submissions"],
    ["Denied", "admin.records.submissions"],    
    ["Expired", "admin.records.submissions"],
  ]

  let breadcrumb = [
    ["Submissions", "admin.records.submissions"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
