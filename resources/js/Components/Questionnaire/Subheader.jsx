import { Link } from '@inertiajs/react';

export default function Subheader({siteConfig, text = "New Submission"}) {
  return (
    <div id="subheader" style={{height: "104px", backgroundColor: siteConfig.themeSubheaderColor, color: siteConfig.themeSubheaderTextColor}}>
      <Link href="/"><div className="flex items-center ml-auto mr-auto" style={{maxWidth: "1140px", paddingTop: "15px"}}>Home &raquo;</div></Link>
      <div className="items-center ml-auto mr-auto" 
        style={{maxWidth: "1140px", paddingTop: "15px", marginTop: "15px", fontSize: "24px", fontWeight: "900"}}>
      {text}
      </div>
    </div>
  );
}