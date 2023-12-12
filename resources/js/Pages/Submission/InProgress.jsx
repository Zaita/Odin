import React from 'react';
import UserLayout from '@/Layouts/UserLayout';

import Questionnaire from '@/Components/Questionnaire';

export default function InProgress(props) {
  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="New Submission" content={<Questionnaire {...props}/>} />
  );
}