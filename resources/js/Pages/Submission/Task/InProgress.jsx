import React from 'react';
import UserLayout from '@/Layouts/UserLayout';

import Questionnaire from '@/Components/Questionnaire';

export default function SubmissionTaskInProgress(props) {

  let questionData = JSON.parse(props.task.task_data).questionnaire.questions;
  let answerData = JSON.parse(props.task.answer_data);
  let updateRoute = "submission.task.update";

  let productName = props.submission.product_name ? props.submission.product_name : "-";

  let breadcrumb = [
    ["Home", "home"],    
    ["Submissions", "submissions"],
    [productName, "submission.submitted", props.submission.uuid],
    [props.task.name, "submission.task.inprogress", props.task.uuid]
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="New Submission" 
      breadcrumb={breadcrumb}
      content={<Questionnaire questionData={questionData} answerData={answerData} 
        updateRoute={updateRoute}
        uuid={props.task.uuid}
        {...props} />} />
  );
}