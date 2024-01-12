import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';

export default function RecordsSubmissionView(props) {  

  function MyContent() {
    return (
      <>    
      <pre>{JSON.stringify(JSON.parse(props.submission.pillar_data), null, 2)}
      *************************************************************************************************************
      *************************************************************************************************************
      {JSON.stringify(JSON.parse(props.submission.questionnaire_data), null, 2)}  
      *************************************************************************************************************
      *************************************************************************************************************
      {JSON.stringify(JSON.parse(props.submission.answer_data), null, 2)}</pre>  
      </>
    );
  }

  let actionMenuItems = [];

  let breadcrumb = [
    ["Submissions", "admin.records.submissions"],
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={actionMenuItems} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
