import React from 'react';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';

import AdminPanel from '@/Layouts/AdminPanel';

export default function ReportView(props) {  
  function MyContent() {
    return (
      <>
      <div>
      <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-11/12 font-bold">Report: {props.title}</div>
        </div>

        <div className="pt-1 pb-1 border-b"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            {props.header.map((field, index) => <div key={"i" + index} className="w-2/12 inline-block font-bold">{field}</div>)}
        </div>
              
        {props.rows.map((row, index) => {
          return (
            <div key={index} className="pt-1 pb-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
              {row.map((field, index2) => <div key={index + "_" + index2}className="w-2/12 inline-block">{field}</div>)}
          </div>)
        })} 
      </div>  
      </>
    );
  }

  return (
    <AdminPanel {...props} content={<MyContent props/>}/>
  );
}
