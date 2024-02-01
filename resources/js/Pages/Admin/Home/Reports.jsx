import React from 'react';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';
import {Link} from '@inertiajs/react';

import AdminPanel from '@/Layouts/AdminPanel';

export default function Reports(props) {  
  function MyContent() {
    return (
      <>
      <div>
      <div className="pb-2 border-b-2"
          style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-11/12 float-left font-bold">Available Reports:</div>
            <div className="font-bold">Actions:</div> 
        </div>
        {props.reports.map((report, index) => {
          return (
            <div key={index} className="pt-1 pb-1 border-b"
            style={{borderColor: props.siteConfig.theme_admin_content_spacer}}>
            <div className="w-11/12 float-left">{report.name}</div>
            <div><Link href={"/admin/home/report/" + report.id}><PlayArrowIcon/></Link></div>
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
