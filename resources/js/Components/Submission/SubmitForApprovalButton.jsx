import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';

/**
 * Determine if we need to show the "Submit for Approval" button or not.
 * We'll only show it if all tasks are either complete or approved.
 */
export default function SubmitForApprovalButton(props) {
  // Only allow the submitter to see the send for approval button
  if (props.auth.user.id != props.submission.submitter_id) {
    return (<></>);
  }

  let error = props.errors && "submit" in props.errors ? (<><ReportIcon/> {props.errors["submit"]}</>) : "";
  if (props.status != "Ready to submit") {
    return (<></>);
  }

  return (
    <>
      <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
      onClick={() => {router.post(route('submission.submitforapproval', [props.submission.uuid], {}))}} 
      >Submit For Approval</ThemedButton>    
      <p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p>    
    </>
  );
}