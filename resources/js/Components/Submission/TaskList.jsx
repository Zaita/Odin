import { router } from '@inertiajs/react'
import ChevronRightIcon  from '@mui/icons-material/ChevronRight';

import {getStatusIcon, getNiceTaskStatus} from '@/Utilities/statusIcons';

export default function TaskList(props) {

  function Content() {
    if (props.tasks.length == 0) {
      return (
        <div className="w-full flex bg-white mb-1 p-2">
          <p><i>
            This submission requires no extra tasks to be completed. If you are ready to send it through for approval,
            please click the 'Submit for Approval' button below.
          </i></p>
        </div>
      );
    } 

    return (
    props.tasks.map((task, index) => {
      return (
      <div key={index} className="w-full flex bg-white mb-1 p-2">
        <div className="w-3/12 pt-2">{task.name}</div>
        <div className="w-2/12 pt-2">{task.time_to_complete}</div>
        <div className="w-2/12 pt-2">{task.time_to_review}</div>
        <div className="w-2/12 pt-2">{task.approved_by}</div>
        <div className="w-2/12">{getNiceTaskStatus(task.status)}</div>
        <div className="w-1/12"><ChevronRightIcon onClick={() => {router.get(route('submission.task', [task.uuid], {}))}}/></div>                    
      </div>
      );
    }))
  }

  // There are tasks to be completed.
  return (
    <div id="task_list" className="mb-2 mt-2 pt-2" style={{borderTop: "2px solid #d9d9d9"}}>
      <div className="text-base font-bold mb-3">Tasks</div>
      <div className="mb-2">Please complete the tasks below. Note that tasks marked with a red asterisk(*) may create new tasks, depending on your answers</div>
        <div className="w-full flex bg-white mb-1 p-2">
          <div className="w-3/12 font-bold">Task</div>
          <div className="w-2/12 font-bold">Time to complete</div>
          <div className="w-2/12 font-bold">Time to review</div>
          <div className="w-2/12 font-bold">Approved by</div>
          <div className="w-2/12 font-bold">Task status</div>
          <div className="w-1/12 font-bold">Actions</div>
        </div>
        <Content/>
    </div>
  );
}