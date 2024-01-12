

export default function ApprovalBox(props) {

  // if (props.submission.status == "submitted") {
  //   return (
  //     <div id="approvals_list" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
  //       <div className="text-base font-bold mb-3">Approvals</div>
  //       <div className="w-3/5 flex bg-white mb-1 p-2">
  //         <i>Note: Required endorsements and approvals will be shown once all tasks have been complete and submission 
  //           has been sent for approval.
  //         </i>
  //       </div>
  //     </div>
  //   );
  // }

  let approvalFlow = JSON.parse(props.submission.approval_flow_data);
  let flowDetails = JSON.parse(approvalFlow.details);

  function buildOutput(element, index) {
    let role = element.type == "group" ? element.group : "-";
    role = element.type == "business_owner" ? "Business Owner" : role;
    
    let name = element.type == "business_owner" ? props.submission.business_owner : "-";

    let approvalStatus = element.approval_status ? element.approval_status : "";
    if (approvalStatus == "") {
      approvalStatus = element.approval_type == "endorsement" ? "Awaiting endorsement" : approvalStatus;
      approvalStatus = element.approval_type == "approval" ? "Awaiting approval" : approvalStatus;
    }

    return (
      <div className="w-3/5 flex bg-white mb-1 p-2" key={index}>
        <div className="w-4/12">{role}</div>
        <div className="w-5/12">{name}</div>
        <div className="w-2/12">{element.approval_type}</div>
        <div className="w-3/12">{approvalStatus}</div>
      </div>
    );
  }

  return (
      <div id="approvals_list" className="mb-6 mt-6 pt-6" style={{borderTop: "2px solid #d9d9d9"}}>
      <div className="text-base font-bold mb-3">Approvals</div>
      <div className="w-3/5 flex bg-white mb-1 p-2">
        <div className="w-4/12 font-bold">Role</div>
        <div className="w-5/12 font-bold">Name</div>
        <div className="w-2/12 font-bold">Type</div>
        <div className="w-3/12 font-bold">Approval status</div>
      </div>
      {flowDetails.flow.map((stage, index) => stage.map((element, index2) => buildOutput(element, index)))}
    </div>
  );
}