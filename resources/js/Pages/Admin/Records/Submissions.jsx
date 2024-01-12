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
        niceStatus = "In Progress";
        break;
      case "in_review":
        niceStatus = "Awaiting Submit";
        break;
      case "submitted":
        niceStatus = "Awaiting Task Completion";
        break;
      default:
        niceStatus = "n/a";
    }
    return niceStatus;
  }

  function MyContent() {
    return (
      <>      
      <div id="submissions" className="mt-5">
        <div id="submission_box">
          <table>
            <thead>
            <tr>
              <th>Date Created</th>
              <th>Pillar</th>
              <th>Product Name</th>
              <th>Tasks Completed</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          {props.submissions.data.map((submission, index) => (
            <tr key={index}>
              <td>{submission.created_at}</td>
              <td>{submission.pillar_name}</td>
              <td>{submission.product_name}</td>
              <td>{submission.tasks_completed}</td>
              <td>{getNiceStatus(submission.status)}</td>
              <td><Link href={"/admin/records/submission/" + submission.id}><ChevronRightIcon/></Link></td>            
            </tr>
          ))}
          </tbody>
          </table>          
        </div>
        <div id="pagination_navbar" className="text-center pt-2" >
          <div className="float-left" style={{fontSize: "11px"}}>Displaying entries {props.submissions.from} to {props.submissions.to} of {props.submissions.total}</div>
          {props.submissions.links.map((link, index) => <a key={index} href={link.url}><span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/></a>)}
        </div>
      </div>
      </>
    );
  }

  let actionMenuItems = [];

  let breadcrumb = [
    ["Submissions", "admin.records.submissions"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
