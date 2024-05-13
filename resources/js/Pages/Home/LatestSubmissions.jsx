import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function LatestSubmissions(props) {
  return (
    <div className="mb-4 w-3/5 ml-auto mr-auto mt-5">
      <div className="text-sm font-bold mt-4 mb-4">Your latest submissions</div>
      <div>
      <div className="w-full flex p-2 mb-0.5" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
        <div className="w-2/12 font-bold">Date Created</div>
        <div className="w-2/12 font-bold">Pillar</div>
        <div className="w-3/12 font-bold">Product Name</div>
        <div className="w-2/12 font-bold">Tasks Completed</div>
        <div className="w-2/12 font-bold">Status</div>
        <div className="w-1/12 font-bold">Actions</div>
      </div>
      {props.latestSubmissions.map((submission, index) => (
        <div className="w-full flex mb-0.5 p-1" style={{backgroundColor: props.siteConfig.theme_content_bg_color}} key={index}>
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
      {props.latestSubmissions.length == 0 && <div className="w-full flex mb-0 p-1 text-center" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
        <i>You have no recent submissions</i>
        </div>}
      </div>
    </div>
  )
}