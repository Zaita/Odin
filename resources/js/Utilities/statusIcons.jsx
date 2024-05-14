import AccessTimeIcon from '@mui/icons-material/AccessTime';
import DescriptionIcon from '@mui/icons-material/Description';
import TimelapseIcon from '@mui/icons-material/Timelapse';
import HourglassBottomIcon from '@mui/icons-material/HourglassBottom';
import VerifiedUserIcon from '@mui/icons-material/VerifiedUser';
import GppBadIcon from '@mui/icons-material/GppBad';
import GppMaybeIcon from '@mui/icons-material/GppMaybe';
import CancelIcon from '@mui/icons-material/Cancel';

export function getStatusIcon(status) {
  switch(status) {
    case "In progress":
      return (<TimelapseIcon style={{width: "34px", color: "orange"}} className="pr-4 w-5"/>); 
    case "Tasks to complete":
      return (<GppMaybeIcon style={{width: "34px", color: "red"}} className="pr-4 w-5"/>); 
    case "Ready to submit":
      return (<DescriptionIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);
    case "Waiting for approval": 
    case "Awaiting security review":
    case "Awaiting business owner approval":
    case "Awaiting certification and accrditation":
    case "Awaiting certification":
    case "Awaiting accreditation":
      return (<HourglassBottomIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);     
    case "Approved":
      return (<VerifiedUserIcon style={{width: "34px", color: "green"}} className="pr-4 w-5"/>);      
    case "Not approved":
      return (<GppBadIcon style={{width: "34px", color: "red"}} className="pr-4 w-5"/>);  
    case "Not Applicable":                
      return (<CancelIcon style={{width: "34px", color: "gray"}} className="pr-4 w-5"/>);  
  }

  return "-";
}

export function getNiceTaskStatus(status) {
  switch(status) {
    case "ready_to_start":
      return (<><AccessTimeIcon style={{width: "34px", color: "red"}}/>To Do</>);
    case "in_progress":
        return (<><TimelapseIcon style={{width: "34px", color: "orange"}}/>In progress</>);         
    case "waiting_for_approval":
      return (<><HourglassBottomIcon style={{width: "34px", color: "blue"}}/>Awaiting approval</>);
    case "approved":
      return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Approved</>);
    case "not_approved":
        return (<><GppBadIcon style={{width: "34px", color: "red"}}/>Not approved</>);       
    case "complete":
      return (<><VerifiedUserIcon style={{width: "34px", color: "green"}}/>Complete</>);  
    case "not_applicable":
      return (<><CancelIcon style={{width: "34px", color: "gray"}}/>Not Applicable</>);          
  }

  return "-";
}