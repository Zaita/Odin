import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function LatestSubmissions(props) {
  return (
    <div id="latest_submissions">
      <div id="heading">Your latest submissions</div>
      <div id="submission_box">
      <div className="w-full flex bg-white p-2">
        <div className="w-2/12 font-bold">Date Created</div>
        <div className="w-2/12 font-bold">Pillar</div>
        <div className="w-3/12 font-bold">Product Name</div>
        <div className="w-2/12 font-bold">Tasks Completed</div>
        <div className="w-2/12 font-bold">Status</div>
        <div className="w-1/12 font-bold">Actions</div>
      </div>
      {props.latestSubmissions.map((submission, index) => (
        <div className="w-full flex bg-white mb-0 p-1" key={index}>
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
      </div>
    </div>
  )
}