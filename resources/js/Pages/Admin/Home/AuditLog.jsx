import React from 'react';
import VisibilityIcon from '@mui/icons-material/Visibility';

import AdminPanel from '@/Layouts/AdminPanel';

export default function AuditLog(props) {  
  console.log("Audit Log");
  function MyContent() {
    return (
      <>
      <div>
      <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-6/12 float-left font-bold">Event:</div>
            <div className="w-2/12 float-left font-bold">User:</div>
            <div className="w-2/12 float-left font-bold">Timestamp:</div> 
            <div className="font-bold">Actions:</div> 
        </div>
        {props.auditLog.data.map((audit, index) => {
          return (
            <div key={index} className="pt-1 pb-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-6/12 float-left">{audit.action}</div>
            <div className="w-2/12 float-left">{audit.user_name} ({audit.user_email})</div>
            <div className="w-2/12 float-left">{audit.created_at}</div>
            <div><VisibilityIcon/></div>
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
