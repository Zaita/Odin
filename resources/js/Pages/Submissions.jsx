import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

import UserLayout from '@/Layouts/UserLayout';

export default function Submissions(props) {
  function Content(props) {
    return (
      <div id="inner_content">
        {/* <div id="submission_box"> */}
        <div className="float-right" style={{fontSize: "11px"}}>Displaying entries {props.submissions.from} to {props.submissions.to} of {props.submissions.total}</div>
          <div className="w-full flex bg-white mb-1 p-2">
              <div className="w-2/12 font-bold">Date Created</div>
              <div className="w-2/12 font-bold">Pillar</div>
              <div className="w-3/12 font-bold">Product Name</div>
              <div className="w-2/12 font-bold">Tasks Completed</div>
              <div className="w-2/12 font-bold">Status</div>
              <div className="w-1/12 font-bold">Actions</div>
          </div>
          {props.submissions.data.map((submission, index) => (
            <div className="w-full flex bg-white mb-1 p-1" key={"x" + index}>
              <div className="w-2/12">{submission.created_at_short}</div>
              <div className="w-2/12">{submission.pillar_name}</div>
              <div className="w-3/12">{submission.product_name}</div>
              <div className="w-2/12">{submission.tasks_completed}</div>
              <div className="w-2/12">{submission.nice_status}</div>
              <div className="w-1/12">
                  <Link href={"/view/" + submission.uuid} style={{color: props.siteConfig.theme_hyperlink_color}}><ChevronRightIcon/></Link>
              </div>
            </div>
          ))}
        {/* </div> */}
        {props.submissions.data.length == 0 && <div className="w-full flex mb-0 p-1 text-center" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
            <i>Nothing awaiting approval</i>
        </div>}           
        <div id="pagination_navbar" className="text-center pt-2 mb-5" >
          {props.submissions.links.map((link, index) => 
            <Link style={{color: props.siteConfig.theme_hyperlink_color}} key={index} href={link.url}>
              <span className="pr-1 pl-1" dangerouslySetInnerHTML={{__html: link.label}}/>
            </Link>)}
        </div>
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Submissions", "submissions"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Submissions" subheaderText="My Submissions" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}