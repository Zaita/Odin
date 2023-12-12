import React, { useRef, useState, Component } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';

export default function AuditLog(props) {  
  console.log("Audit Log");
  function MyContent() {
    return (
      <>
      <div>
        <div style={{borderBottom: "3px solid white"}}>
            <div className="w-1/2 float-left font-bold">Action:</div>
            <div className="w-1/5 float-left font-bold">User:</div>
            <div className="font-bold">Timestamp:</div> 
        </div>
        {props.auditLog.data.map((audit, index) => {
          return (
            <div key={index} style={{borderBottom: "1px solid white"}} className="pt-1">
            <div className="w-1/2 float-left">{audit.action}</div>
            <div className="w-1/5 float-left">{audit.user_name} ({audit.user_email})</div>
            <div>{audit.created_at}</div>
          </div>)
        })} 
        <div id="pagination_navbar" className="text-center pt-2" >
          <div className="float-left" style={{fontSize: "11px"}}>Displaying entries {props.auditLog.from} to {props.auditLog.to} of {props.auditLog.total}</div>
          {props.auditLog.links.map((link, index) => <a key={index} href={link.url}><span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/></a>)}
        </div>       
      </div>  
      </>
    );
  }

  return (
    <AdminPanel {...props} content={<MyContent props/>}/>
  );
}
