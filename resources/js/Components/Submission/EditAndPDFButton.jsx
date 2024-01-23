import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';

export default function EditAndPDFButton(props) {
  if (props.submission.status == "submitted" && props.auth.user.id == props.submission.submitter_id) {
    return (<>
      <ThemedButton siteConfig={props.siteConfig} className="ml-2"
      onClick={() => {router.get(route('submission.edit', [props.submission.uuid], {}))}} 
      >Edit</ThemedButton>

      {/* <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
      onClick={() => {router.get(route('submission.downloadpdf', [props.submission.uuid], {}))}} 
      >PDF</ThemedButton> */}
    </>)
  }

  return (<>
    <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
    onClick={() => {router.get(route('submission.downloadpdf', [props.submission.uuid], {}))}} 
    >PDF</ThemedButton>
  </>)
}