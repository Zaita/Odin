import { Link } from '@inertiajs/react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function Submissions({latestSubmissions}) {

  function getNiceStatus(status) {
    let niceStatus = "";
    switch(status) {
      case "in_progress":
        niceStatus = "In Progress";
        break;
      case "in_review":
        niceStatus = "Awaiting Submit";
        break;
      case "submitted":
        niceStatus = "Awaiting Task Completion";
        break;
      default:
        niceStatus = "n/a";
    }
    return niceStatus;
  }


  return (
    <div id="latest_submissions">
      <div id="heading">Your latest submissions</div>
      <div id="submission_box">
        <table>
          <thead>
          <tr>
            <th>Date Created</th>
            <th>Pillar</th>
            <th>Product Name</th>
            <th>Tasks Completed</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        {latestSubmissions.map((submission, index) => (
          <tr key={index}>
            <td>{submission.created_at_short}</td>
            <td>{submission.pillar_name}</td>
            <td>{submission.product_name}</td>
            <td>{submission.tasks_completed}</td>
            <td>{getNiceStatus(submission.status)}</td>
            <td><Link href={"/view/" + submission.uuid}><ChevronRightIcon/></Link></td>            
          </tr>
        ))}
        </tbody>
        </table>
      </div>
    </div>
  )
}