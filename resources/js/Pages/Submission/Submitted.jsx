import {React, useState} from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router, Link } from '@inertiajs/react'
import ChevronRightIcon  from '@mui/icons-material/ChevronRight';
import AccessTimeIcon from '@mui/icons-material/AccessTime';
import TimelapseIcon from '@mui/icons-material/Timelapse';
import HourglassBottomIcon from '@mui/icons-material/HourglassBottom';
import VerifiedUserIcon from '@mui/icons-material/VerifiedUser';
import GppBadIcon from '@mui/icons-material/GppBad';
import GppMaybeIcon from '@mui/icons-material/GppMaybe';
import ReportIcon from '@mui/icons-material/Report';

import DescriptionIcon from '@mui/icons-material/Description';
import ApprovalBox from '@/Components/Submission/ApprovalBox';
import SubmitForApprovalButton from '@/Components/Submission/SubmitForApprovalButton';
import AssignToMeButton from '@/Components/Submission/AssignToMeButton';
import ApproveOrEndorseButton from '@/Components/Submission/ApproveOrEndorseButton';
import EditAndPDFButton from '@/Components/Submission/EditAndPDFButton';
import AddCollaboratorModal from '@/Components/Submission/AddCollaborator';
import {getStatusIcon, getNiceTaskStatus} from '@/Utilities/statusIcons';
import TaskList from '@/Components/Submission/TaskList';

function Content(props) {
  let [addCollabDialogIsOpen, setAddCollabDialogIsOpen] = useState(false);

  function openDialog(e) {
    e.preventDefault();
    setAddCollabDialogIsOpen(true)
  }

  let error = props.errors && "error" in props.errors ? (<><ReportIcon/> {props.errors["error"]}</>) : "";
  let ticket = props.submission.ticket_link ? (<><b>Ticket:</b> {props.submission.ticket_link}<br/></>) : "";
  let releaseDate = props.submission.release_date ? (<><b>Go live date:</b> {props.submission.release_date}<br/></>) : "";

  return (
    <div id="inner_content">
      <AddCollaboratorModal open={addCollabDialogIsOpen} onCancel={() => setAddCollabDialogIsOpen(false)} {...props}/>
      <div id="heading" className="text-lg mb-2 pt-2 font-bold">Submission details</div>
      <div id="summary" className="flex">
        <div id="summary_text" className="w-1/2">
          <b>Pillar:</b> {props.submission.pillar_name}<br/>
          {ticket}
          <b>Submission created:</b> {props.submission.created_at_short}<br/>
          {releaseDate}
        </div>
        <div id="submission_info" className="w-1/2">
          <div id="submission_status" className="align-middle mb-3"
            style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>{getStatusIcon(props.status)}
            <span className="pt-1">{props.status}</span>
          </div>
          <br/>
          <b>Submitted by:</b> {props.submission.submitter_name}<br/>
          <b>Email: </b> {props.submission.submitter_email}
        </div>
      </div>
      <div id="collaborators" className="mb-2 mt-2 pt-2" style={{borderTop: "2px solid #d9d9d9"}}>
        <div className="text-base font-bold mb-3">Collaborators</div>
        <div className="flex">
          <div className=" w-1/2">
            <div>You can add people to help complete your submission.</div>
            <div> Please contact the security team for more information.</div>
            <div id="add_collaborators"><Link href={void(0)} onClick={(e) => openDialog(e)}>+ Add Collaborators</Link></div>
          </div>
          <div className="w-1/2">
            <div><b>Assigned collaborators:</b></div>
            <div>
            {
              props.collaborators?.map((collaborator, index) => {
                return(
                  <div key={"collab_" + index}>{collaborator.user.name}</div>
                );
              })
            }
            </div>
          </div>          
        </div>
      </div>
      <TaskList {...props}/>
      <ApprovalBox {...props}/>
      <div><p id="error" style={{color: props.siteConfig.theme_subheader_color}}>{error}</p> </div>
      <div id="bottom">
        <div className="inline-block w-1/2">
          <EditAndPDFButton {...props}/>
        </div>
        <div className="inline-block w-1/2">
          <span className="float-right">
              {<SubmitForApprovalButton {...props}/>}
              {<AssignToMeButton {...props}/>}
              {<ApproveOrEndorseButton {...props}/>}
            </span>
        </div>
      </div>
      <div className="h-4">&nbsp;</div>
    </div>
  )
}

export default function Submitted(props) {
  let productName = props.submission.product_name ? props.submission.product_name : "-";

  let breadcrumb = [
    ["Home", "home"],    
    ["Submissions", "submissions"],
    [productName, "submission.submitted", props.submission.uuid]
  ]
  return (
    <UserLayout user={props.auth.user} siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText={productName} 
    breadcrumb={breadcrumb}
    content={<Content {...props} />}/>
    );
}