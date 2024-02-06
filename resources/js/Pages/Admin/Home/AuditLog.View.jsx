import React from 'react';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';

import AdminPanel from '@/Layouts/AdminPanel';

export default function AuditLog_View(props) {  
  function MyContent() {
    return (
      <div>
        <div className="pb-2 w-11/12">Action: {props.entry.action}</div>
        <div className="pb-2 w-11/12">User name: {props.entry.user_name}</div>
        <div className="pb-2 w-11/12">User email: {props.entry.user_email}</div>
        <div className="w-11/12">Request:</div>
        <div className="pb-2 w-11/12">{props.entry.request}</div>
      </div>  
    );
  }

  let breadcrumb = [
    ["Audit Log", "admin.home.auditlog"],
  ];

  return (
    <AdminPanel {...props} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
