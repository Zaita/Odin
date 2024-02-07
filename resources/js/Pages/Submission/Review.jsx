import React from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';

function Content(props) {
  let questions = JSON.parse(props.submission.questionnaire_data);
  let answers = JSON.parse(props.submission.answer_data);

  function getQuestionContent(questionTitle, questionHeading, index) {
    let userResponses = [];    
    answers.answers.map((response) => {
      if (response.question == questionTitle) {
        if (response.status == "not_applicable") {
          userResponses.push(<div key={index} className="pl-7"><i>not applicable</i></div>)
        } else {
          response.data.map((answer, index) => {
            if (typeof(answer.value) == "string" && answer.value?.includes("\n")) {
              userResponses.push(<div key={index} className="pl-7">{answer.value}</div>);

            } else if (typeof answer.value == "object") {
              let output = [];
              Object.entries(answer.value).map(([key, val], index) => {
                if (val) {
                  output.push(<p key={"cb"+key+index}>- {key}</p>)
                }
              });
              userResponses.push(<div key={index} className="pl-7"><span id="answer_heading" className="font-extrabold">{answer.field}</span>: {output}</div>);

            } else {
              userResponses.push(<div key={index} className="pl-7"><span id="answer_heading" className="font-extrabold">{answer.field}</span>: {answer.value ? answer.value : "-"}</div>)
            }
          })
        }
    }});
    
    return(
    <div id="response" key={index}>
        <div id="heading" className="font-extrabold">{index}. {questionHeading}</div>
        {userResponses.map((response, idx) => <span key={idx}>{response}</span>)}
      </div>
    )
  }

  function RiskTable() {
    // This is not configured for displaying a risk table
    if (props.submission.type == "questionnaire" || props.submission.risk_calculation == "none") {
      return <></>
    }

    let riskData = JSON.parse(props.submission.risk_data);
    let output = [];
    riskData.map((risk, index) => output.push(
      <div key={index}>
        <div className="inline-block w-2/12">{risk["name"]}</div>
        <div className="inline-block w-2/12">{risk["score"]}</div>
        <div className="inline-block w-8/12">{risk["description"]}</div>       
      </div>) );

    return <div>{output}</div>
  }

  return (
    <div id="inner_content">
      <div id="review_responses">
        {questions.map((question, index) => (
          getQuestionContent(question.title, question.heading, index+1)
          ))
        }
      </div>
      <RiskTable/>
      <div id="review_actions">
        <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.visit(route('submission.inprogress', [props.submission.uuid], {}))}} 
          >Edit</ThemedButton>
        {/* <ThemedButton siteConfig={props.siteConfig} className="ml-2">PDF</ThemedButton> */}
        <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
        onClick={() => {router.post(route('submission.submit', [props.submission.uuid], {}))}} 
        >Submit Questionnaire</ThemedButton>
      </div>
    </div>
  )
}

export default function Review(props) {
  let breadcrumb = [
    ["Home", "home"],    
    ["Current submission", "submission.inprogress", props.submission.uuid],
    ["Review", "submission.review", props.submission.uuid],
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="Review Submission" 
      breadcrumb={breadcrumb}
      content={<Content {...props} />}/>
    );
}