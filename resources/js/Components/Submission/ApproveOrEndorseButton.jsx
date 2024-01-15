import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';

export default function ApproveOrEndorseButton(props) {
  if (props.can_approve_with_type == null || props.submission.status != "waiting_for_approval")
    return (<></>)

  if (props.can_approve_with_type == "endorsement") {
    return (
      <>
        <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.post(route('submission.sendback', [props.submission.uuid], {}))}} 
          >Send back for changes</ThemedButton>
          <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.post(route('submission.deny', [props.submission.uuid], {}))}} 
          >Not endorsed</ThemedButton>
          <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.post(route('submission.approve', [props.submission.uuid], {}))}} 
          >Endorsed</ThemedButton>
      </>
    )
  } else if (props.can_approve_with_type == "approval") {
    return (
      <>
        <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.post(route('submission.sendback', [props.submission.uuid], {}))}} 
          >Send back for changes</ThemedButton>
          <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.post(route('submission.deny', [props.submission.uuid], {}))}} 
          >Not approved</ThemedButton>
          <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
          onClick={() => {router.post(route('submission.approve', [props.submission.uuid], {}))}} 
          >Approved</ThemedButton>
      </>
    )
  }

  return (
    <>
    </>
  )
}