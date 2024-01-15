import HourglassBottomIcon from '@mui/icons-material/HourglassBottom';
import VerifiedUserIcon from '@mui/icons-material/VerifiedUser';
import GppBadIcon from '@mui/icons-material/GppBad';
import CancelIcon from '@mui/icons-material/Cancel';

export default function ApprovalBox(props) {
  /**
   * If the submission is not in the right status, just return a message to the user
   */
  if (props.submission.status == "submitted") {
    return (
      <div id="approvals_list" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
        <div className="text-base font-bold mb-3">Approvals</div>
        <div className="w-3/5 flex bg-white mb-1 p-2">
          <i>Note: Required endorsements and approvals will be shown once all tasks have been complete and submission 
            has been sent for approval.
          </i>
        </div>
      </div>
    );
  }

  /**
   * Get us a nice pretty icon for the approval status
   */
  function getApprovalStatusIcon(status) {
    switch(status) {
      case "in_review": 
        return (<><HourglassBottomIcon style={{width: "34px", color: "blue"}}/>In review</>);
      case "approved":
        return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Approved</>);
      case "not_approved":
        return (<><GppBadIcon style={{width: "34px", color: "red"}}/>Not approved</>);   
      case "endorsed":
        return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Endorsed</>);
      case "not_endorsed":
        return (<><GppBadIcon style={{width: "34px", color: "red"}}/>Not endorsed</>);  
      case "awaiting_endorsement":      
        return (<><HourglassBottomIcon style={{width: "34px", color: "blue"}}/>Awaiting endorsement</>);                        
      case "awaiting_approval":
        return (<><HourglassBottomIcon style={{width: "34px", color: "blue"}}/>Awaiting approval</>); 
      case "approval_expired":                       
        return (<><CancelIcon style={{width: "34px", color: "gray"}}/>Approval expired</>);  
      case "endorsement_expired":                       
        return (<><CancelIcon style={{width: "34px", color: "gray"}}/>Endorsement expired</>);  
    }

    return "XX";
  }

  let approvalStages = props.submission.approval_stages;
  
  // let approvalFlow = JSON.parse(props.submission.approval_flow_data);
  // let flowDetails = JSON.parse(approvalFlow.details);

  function buildOutput(element, index) {
    let role = element.type == "group" ? element.target : "-";
    role = element.type == "business_owner" ? "Business Owner" : role;
    
    let name = element.type == "business_owner" ? props.submission.business_owner : "";
    name = element.assigned_to_user_name ? element.assigned_to_user_name : name;
    name = element.approver_name ? element.approver_name : name;
    name = name == "" ? "-" : name

    let approvalStatus = element.status ? element.status : "";
    if (approvalStatus == "") {
      approvalStatus = element.approval_type == "endorsement" ? "awaiting_endorsement" : approvalStatus;
      approvalStatus = element.approval_type == "approval" ? "awaiting_approval" : approvalStatus;
      approvalStatus = element.assigned_to_user_name ? "in_review" : approvalStatus;
      approvalStatus = element.approval_status ? element.approval_status : approvalStatus;

    }
    let idx = `approval_box_${index}`;

    return (
      <div className="w-3/5 flex bg-white mb-1 p-2" key={idx}>
        <div className="w-4/12">{role}</div>
        <div className="w-5/12">{name}</div>
        <div className="w-3/12">{getApprovalStatusIcon(approvalStatus)}</div>
      </div>
    );
  }

  return (
      <div id="approvals_list" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
      <div className="text-base font-bold mb-3">Approvals</div>
      <div className="w-3/5 flex bg-white mb-1 p-2">
        <div className="w-4/12 font-bold">Role</div>
        <div className="w-5/12 font-bold">Name</div>
        <div className="w-3/12 font-bold">Approval status</div>
      </div>
      {approvalStages.map((stage, index) => buildOutput(stage, index))}
    </div>
  );
}