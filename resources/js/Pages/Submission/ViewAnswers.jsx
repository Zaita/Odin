import React from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';
import Questionnaire_RiskTable from '@/Components/Questionnaire/RiskTable';

function Content(props) {
  let questions = JSON.parse(props.submission.questionnaire_data);
  let answers = JSON.parse(props.submission.answer_data);

  function getQuestionContent(questionTitle, questionHeading, index) {
    let userResponses = [];    
    answers.answers.map((response) => {
      if (response.question == questionTitle) {
        if (response.status == "not_applicable") {
          userResponses.push(<div key={index} className="pl-1"><i>not applicable</i></div>)
        } else {
          response.data.map((answer, index) => {
            if (typeof(answer.value) == "string" && answer.value?.includes("\n")) {
              userResponses.push(<div key={index} className="pl-1">{answer.value}</div>);

            } else if (typeof answer.value == "object") {
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
      <div key={index}>
        <div className="inline-block w-4/12 font-extrabold align-top pl-1">{index}. {questionHeading}</div>
        <div className="inline-block w-8/12" style={{borderLeft: "2px solid " + props.siteConfig.theme_bg_color}}>
          {userResponses.map((response, idx) => <span key={idx}>{response}</span>)}
        </div>
      </div>
    )
  }

  return (
    <div id="inner_content">
      <div className="mb-2 pt-2 pb-2"
        style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
        {questions.map((question, index) => (
          getQuestionContent(question.title, question.heading, index+1)
          ))
        }
      </div>
      <Questionnaire_RiskTable {...props}/>
      <div id="review_actions">
        <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
        onClick={() => {router.get(route('submission.view', [props.submission.uuid], {}))}} 
        >Submission Details</ThemedButton>
      </div>
    </div>
  )
}

export default function ViewAnswers(props) {
  let breadcrumb = [
    ["Home", "home"],    
    ["Current submission", "submission.view", props.submission.uuid],
    ["View Answers", "submission.viewanswers", props.submission.uuid],
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="View Submission Answers" 
      breadcrumb={breadcrumb}
      content={<Content {...props} />}/>
    );
}