import React from 'react';
import UserLayout from '@/Layouts/UserLayout';

import Questionnaire from '@/Components/Questionnaire';

export default function InProgress(props) {
  
  let questionData = JSON.parse(props.submission.questionnaire_data);
  let answerData = JSON.parse(props.submission.answer_data);
  let updateRoute = "submission.update";

  
let breadcrumb = [
  ["Home", "home"],    
  ["Current submission", "submission.inprogress", props.submission.uuid]
]

return (
  <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="New Submission" 
    breadcrumb={breadcrumb}
    content={<Questionnaire questionData={questionData} answerData={answerData} updateRoute={updateRoute} uuid={props.submission.uuid} {...props} />} 
  />
);
}