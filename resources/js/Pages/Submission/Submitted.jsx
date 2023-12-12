import React from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';
import DescriptionIcon from '@mui/icons-material/Description';

function Content(props) {
  return (
    <div id="inner_content">
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
          <div id="submission_status" className="align-middle mb-3"><DescriptionIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>
            <span className="pt-1">Ready to submit</span>
          </div>
          <br/>
          <b>Submitted by:</b> {props.submission.submitter_name}<br/>
          <b>Email: </b> {props.submission.submitter_email}
        </div>
      </div>
      <div id="collaborators" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
        <div className="text-base font-bold mb-5">Collaborators</div>
        <div>You can add people to help complete your submission. Please contact the security team for more information.</div>
        <div id="add_collaborators">+ Add Collaborators</div>
      </div>
      <div id="task_list">

      </div>
      <div id="bottom">
        <div id="left">
          <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.visit(route('submission.inprogress', [props.submission.uuid], {}))}} 
          >Edit</ThemedButton>
          <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.get(route('submission.submit', [props.submission.uuid], {}))}} 
          >PDF</ThemedButton>
        </div>
        <div id="right">
        <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.get(route('submission.submit', [props.submission.uuid], {}))}} 
          >Submit For Approval</ThemedButton>          
        </div>
      </div>

    </div>
  )
}

export default function Submitted(props) {
  let productName = props.submission.product_name;
  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText={productName} content={<Content {...props} />}/>
    );
}