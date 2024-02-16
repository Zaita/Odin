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
      <div className="font-bold text-lg">{pillar.name}</div>
      <div>{date}</div>
      <div>{auth.user.name} ({auth.user.email})</div>
      <div className="pt-4 pb-2 font-bold text-base">Key Information</div> 
      <div className="whitespace-pre-wrap p-2"
        style={{backgroundColor: siteConfig.theme_content_bg_color}}
        dangerouslySetInnerHTML={{__html: pillar.key_information}} />
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