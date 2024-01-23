import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

import UserLayout from '@/Layouts/UserLayout';

export default function Approvals(props) {
  function Content(props) {
    return (
      <div id="content_box" className="mt-5">
        <div className="float-right" style={{fontSize: "11px"}}>Displaying entries</div>
          <div className="w-full flex bg-white mb-1 p-2">
              <div className="w-2/12 font-bold">Date Created</div>
              <div className="w-2/12 font-bold">Pillar</div>
              <div className="w-3/12 font-bold">Product Name</div>
              <div className="w-2/12 font-bold">Tasks Completed</div>
              <div className="w-2/12 font-bold">Submitter</div>
              <div className="w-1/12 font-bold">Actions</div>
          </div>
          {props.submissions.map((submission, index) => (
            <div className="w-full flex bg-white mb-0 p-1">
              <div className="w-2/12">{submission.created_at_short}</div>
              <div className="w-2/12">{submission.pillar_name}</div>
              <div className="w-3/12">{submission.product_name}</div>
              <div className="w-2/12">{submission.tasks_completed}</div>
              <div className="w-2/12">{submission.submitter_name}</div>
              <div className="w-1/12">
                  <Link href={"/view/" + submission.uuid} style={{color: props.siteConfig.theme_hyperlink_color}}><ChevronRightIcon/></Link>
              </div>
            </div>
          ))}
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Approvals", "approvals"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Approvals" subheaderText="My Approvals" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}