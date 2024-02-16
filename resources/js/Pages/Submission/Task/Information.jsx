import React from 'react';
import { Link } from '@inertiajs/react';
import ThemedButton from '@/Components/ThemedButton';
import { router } from '@inertiajs/react'
import UserLayout from '@/Layouts/UserLayout';

function Content(props) {
  function BackToSubmission(uuid) {
    router.get(route("submission.submitted", uuid={uuid}))
  }
  function StartSubmission(uuid) {
    router.post(route('submission.task.start', uuid={uuid}));
  }

  const current = new Date();
  const date = `${current.getDate()}/${current.getMonth()+1}/${current.getFullYear()}`;

  return ( 
    <div id="inner_content">
      <div id="small_title">{props.task.name}</div>
      <div id="pillar_start_date">{date}</div>
      <div id="pillar_start_user">{props.auth.user.name} ({props.auth.user.email})</div>
      <div id="pillar_key_information">Key Information</div> 
      <div id="pillar_key_information_text" 
        style={{backgroundColor: props.siteConfig.theme_content_bg_color}}
        dangerouslySetInnerHTML={{__html: props.task.key_information}} />
      <div id="pillar_start_button">
        <ThemedButton className="float-left" siteConfig={props.siteConfig} onClick={() => BackToSubmission(props.submission.uuid)}>Submission</ThemedButton>
        <ThemedButton className="float-right" siteConfig={props.siteConfig} onClick={() => StartSubmission(props.task.uuid)}>Start &#62;</ThemedButton>
      </div>
    </div>
  );
};

export default function SubmissionTaskInformation(props) {
  let productName = props.submission.product_name ? props.submission.product_name : "-";

  let breadcrumb = [
    ["Home", "home"],    
    ["Submissions", "submissions"],
    [productName, "submission.submitted", props.submission.uuid],
    [props.task.name, "submission.task", props.task.uuid]
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="Start Task" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}

 