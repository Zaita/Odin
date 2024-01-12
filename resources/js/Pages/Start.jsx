import React from 'react';
import { Link } from '@inertiajs/react';
import ThemedButton from '@/Components/ThemedButton';
import { router } from '@inertiajs/react'
import UserLayout from '@/Layouts/UserLayout';

function StartSubmission(pillarId) {
  router.post(route('submission.start', pillarId={pillarId}));
}

function Content({auth, siteConfig, pillar}) {
  const current = new Date();
  const date = `${current.getDate()}/${current.getMonth()+1}/${current.getFullYear()}`;

  return ( 
    <div id="inner_content">
      <div id="small_title">{pillar.name}</div>
      <div id="pillar_start_date">{date}</div>
      <div id="pillar_start_user">{auth.user.name} ({auth.user.email})</div>
      <div id="pillar_key_information">Key Information</div> 
      <div id="pillar_key_information_text" dangerouslySetInnerHTML={{__html: pillar.key_information}} />
      <div id="pillar_start_button">
        <ThemedButton className="float-right" siteConfig={siteConfig} onClick={() => StartSubmission(pillar.id)}>Start &#62;</ThemedButton>
      </div>
    </div>
  );
}

export default function Start(props) {
  let breadcrumb = [
    ["Home", "home"],    
    ["Start Submission", route().current(), props.pillar.id],
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="New Submission" 
      breadcrumb={breadcrumb} 
      content={<Content {...props}/>} />
  );
}