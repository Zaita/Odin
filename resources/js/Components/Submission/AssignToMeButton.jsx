import { router } from '@inertiajs/react'
import ReportIcon from '@mui/icons-material/Report';

import ThemedButton from '@/Components/ThemedButton';

/**
 * Determine if we need to show an "Assign to me" button or not.
 * The assign to me button is used by approvers of the submission to assign it to themselves
 * to prevent any race conditions with approvers
 */
export default function AssignToMeButton(props) {
  let error = props.errors && "approval" in props.errors ? (<><ReportIcon/> {props.errors["approval"]}</>) : "";
  // Show nothing if we're not awaiting approval
  if (props.submission.status != "waiting_for_approval") {
    return (<></>);
  }
  // Check if person is an endorser. 
  if (!props.is_an_approver || !props.can_be_assigned) {
    return (<></>);    
  }
  // Show assign to me button
  return (
    <>
      <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
      onClick={() => {router.post(route('submission.assigntome', [props.submission.uuid], {}))}} 
      >Assign to me</ThemedButton>    
      <p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p>    
    </>
  );
}