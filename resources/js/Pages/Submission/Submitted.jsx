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

function Content(props) {
  let [addCollabDialogIsOpen, setAddCollabDialogIsOpen] = useState(false);

  function getStatusIcon(status) {
    switch(status) {
      case "In progress":
        return (<TimelapseIcon style={{width: "34px", color: "orange"}} className="pr-4 w-5"/>); 
      case "Tasks to complete":
        return (<GppMaybeIcon style={{width: "34px", color: "red"}} className="pr-4 w-5"/>); 
      case "Ready to submit":
        return (<DescriptionIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);
      case "Waiting for approval": 
      case "Awaiting security review":
      case "Awaiting business owner approval":
      case "Awaiting certification and accrditation":
      case "Awaiting certification":
      case "Awaiting accreditation":
        return (<HourglassBottomIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);     
      case "Approved":
        return (<VerifiedUserIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);      
      case "Not approved":
        return (<GppBadIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);                  
    }

    return "XX";
  }

  function getNiceTaskStatus(status) {
    switch(status) {
      case "ready_to_start":
        return (<><AccessTimeIcon style={{width: "34px", color: "red"}}/>To Do</>);
      case "in_progress":
          return (<><TimelapseIcon style={{width: "34px", color: "orange"}}/>In progress</>);         
      case "waiting_for_approval":
        return (<><HourglassBottomIcon style={{width: "34px", color: "blue"}}/>Awaiting approval</>);
      case "approved":
        return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Approved</>);
      case "not_approved":
          return (<><GppBadIcon style={{width: "34px", color: "red"}}/>Not approved</>);       
      case "complete":
        return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Complete</>);    
    }

    return "-";
  }

  /**
   * Get the Task List object. If there are no tasks, then display a message that there are no
   * tasks and the submission can be sent for approval straight away
   */
  function TaskList() {
    if (props.tasks.length == 0) {
      return (
        <div className="w-full flex bg-white mb-1 p-2">
          <p><i>
            This submission requires no extra tasks to be completed. If you are ready to send it through for approval,
            please click the 'Submit for Approval' button below.
          </i></p>
        </div>
      );
    }

    // There are tasks to be completed.
    return (
      <>
      {props.tasks.map((task, index) => {
        return (
          <div key={index} className="w-full flex bg-white mb-1 p-2">
            <div className="w-3/12 pt-2">{task.name}</div>
            <div className="w-2/12 pt-2">{task.time_to_complete}</div>
            <div className="w-2/12 pt-2">{task.time_to_review}</div>
            <div className="w-2/12 pt-2">{task.approved_by}</div>
            <div className="w-2/12">{getNiceTaskStatus(task.status)}</div>
            <div className="w-1/12"><ChevronRightIcon onClick={() => {router.get(route('submission.task', [task.uuid], {}))}}/></div>                    
          </div>
        );
      })}
      </>
    );
  }

  function openDialog(e) {
    e.preventDefault();
    setAddCollabDialogIsOpen(true)
  }

  let error = props.errors && "error" in props.errors ? (<><ReportIcon/> {props.errors["error"]}</>) : "";

  return (
    <div id="inner_content">
      <AddCollaboratorModal open={addCollabDialogIsOpen} onCancel={() => setAddCollabDialogIsOpen(false)} {...props}/>
      <div id="heading" className="text-lg mb-6 pt-5 font-bold">Submission details</div>
      <div id="summary" className="flex">
        <div id="summary_text" className="w-1/2">
          <b>{props.submission.product_name}</b><br/>
          {props.submission.pillar_name}<br/>
          <br/>
          <b>Ticket:</b> {props.submission.ticket_link}<br/>
          <b>Submission created:</b> {props.submission.created_at_short}<br/>
          <b>Go live date:</b> {props.submission.release_date}<br/>
        </div>
        <div id="submission_info" className="w-1/2">
          <div id="submission_status" className="align-middle mb-3">{getStatusIcon(props.status)}
            <span className="pt-1">{props.status}</span>
          </div>
          <br/>
          <b>Submitted by:</b> {props.submission.submitter_name}<br/>
          <b>Email: </b> {props.submission.submitter_email}
        </div>
      </div>
      <div id="collaborators" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
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
      <div id="task_list" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
        <div className="text-base font-bold mb-3">Tasks</div>
        <div className="mb-2">Please complete the tasks below. Note that tasks marked with a red asterisk(*) may create new tasks, depending on your answers</div>
          <div className="w-full flex bg-white mb-1 p-2">
            <div className="w-3/12 font-bold">Task</div>
            <div className="w-2/12 font-bold">Time to complete</div>
            <div className="w-2/12 font-bold">Time to review</div>
            <div className="w-2/12 font-bold">Approved by</div>
            <div className="w-2/12 font-bold">Task status</div>
            <div className="w-1/12 font-bold">Actions</div>
          </div>
          <TaskList/>

      </div>
      <ApprovalBox {...props}/>
      <div><p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p> </div>
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