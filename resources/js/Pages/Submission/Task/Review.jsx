import React from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';
import Questionnaire_RiskTable from '@/Components/Questionnaire/RiskTable';

function Content(props) {
  let questions = props.questions;
  let answers = JSON.parse(props.task.answer_data);

  function getQuestionContent(questionTitle, questionHeading, index) {
    let userResponses = [];    
    answers.answers.map((response) => {
      if (response.question == questionTitle) {
        if (response.status == "not_applicable") {
          userResponses.push(<div key={index} className="pl-1"><i>not applicable</i></div>)
        } else {
          response.data.map((answer, index) => {
            if (typeof(answer.value) == "string" && answer.value?.includes("\n")) {
              userResponses.push(<div key={index} className="pl-1"><span id="answer_heading" className="font-extrabold">{answer.field}</span>:<br/>{answer.value}</div>);

            } else if (answer.value != null && typeof answer.value == "object") {
              let output = [];
              Object.entries(answer.value).map(([key, val], index) => {
                if (val) {
                  output.push(<p key={"cb"+key+index}>- {key}</p>)
                }
              });
              userResponses.push(<div key={index} className="pl-1"><span id="answer_heading" className="font-extrabold">{answer.field}</span>: {output}</div>);

            } else {
              userResponses.push(<div key={index} className="pl-1"><span id="answer_heading" className="font-extrabold">{answer.field}</span>: {answer.value ? answer.value : "-"}</div>)
            }
          })
        }
    }});
    
    return(
      <div key={index} className="mb-1 min-h-10 p-2" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
        <div className="inline-block w-4/12 align-top pl-1 pr-2 min-h-10" style={{borderRight: "1px solid " + props.siteConfig.theme_bg_color}}>
          <span className="font-extrabold">{index}.</span> {questionHeading}</div>
        <div className="inline-block w-8/12 min-h-10" style={{borderLeft: "1px solid " + props.siteConfig.theme_bg_color}}>
          {userResponses.map((response, idx) => <span key={idx}>{response}</span>)}
        </div>
      </div>
    )
  }

  return (
    <div id="inner_content">
      <span className="text-lg font-bold">User responses</span>
      <div className="mb-2 p-2">
        {questions.map((question, index) => (
          getQuestionContent(question.title, question.heading, index+1)
          ))
        }
      </div>
      <Questionnaire_RiskTable {...props}/>
      <div id="review_actions">
        <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.visit(route('submission.task.inprogress', [props.task.uuid], {}))}} 
          >Edit</ThemedButton>
        {/* <ThemedButton siteConfig={props.siteConfig} className="ml-2">PDF</ThemedButton> */}
        <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
        onClick={() => {router.post(route('submission.task.submit', [props.task.uuid], {}))}} 
        >Submit Questionnaire</ThemedButton>
      </div>
    </div>
  )
}

export default function SubmissionTaskReview(props) {
  let productName = props.submission.product_name ? props.submission.product_name : "-";

  let breadcrumb = [
    ["Home", "home"],    
    ["Submissions", "submissions"],
    [productName, "submission.submitted", props.submission.uuid],
    [props.task.name, "submission.task.review", props.task.uuid]
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="Review Task" 
      breadcrumb={breadcrumb}
      content={<Content {...props} />}/>
    );
}