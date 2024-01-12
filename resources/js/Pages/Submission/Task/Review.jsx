import React from 'react';
import UserLayout from '@/Layouts/UserLayout';
import { router } from '@inertiajs/react'

import ThemedButton from '@/Components/ThemedButton';

function Content(props) {
  let questions = props.questions;
  let answers = JSON.parse(props.task.answer_data);

  function getQuestionContent(questionTitle, questionHeading, index) {
    let userResponses = [];    
    answers.answers.map((response) => {
      if (response.question == questionTitle) {
        if (response.status == "not_applicable") {
          userResponses.push(<div key={index} className="pl-7">not applicable</div>)
        } else {
          response.data.map((answer, index) => {
            if (answer.value.includes("\n")) {
              userResponses.push(<div key={index} className="pl-7">{answer.value}</div>)
            } else {
              userResponses.push(<div key={index} className="pl-7"><span id="answer_heading" className="font-extrabold">{answer.field}</span> - {answer.value}</div>)
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

  return (
    <div id="inner_content">
      <div id="review_responses">
        {questions.map((question, index) => (
          getQuestionContent(question.title, question.heading, index+1)
          ))
        }
      </div>
      <div id="review_actions">
        <ThemedButton siteConfig={props.siteConfig} className="ml-2"
          onClick={() => {router.visit(route('submission.task.inprogress', [props.task.uuid], {}))}} 
          >Edit</ThemedButton>
        {/* <ThemedButton siteConfig={props.siteConfig} className="ml-2">PDF</ThemedButton> */}
        <ThemedButton siteConfig={props.siteConfig} selected className="ml-2"
        onClick={() => {router.get(route('submission.task.submit', [props.task.uuid], {}))}} 
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