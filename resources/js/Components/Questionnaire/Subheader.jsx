import { Link } from '@inertiajs/react';

export default function Subheader({siteConfig, text = "New Submission", breadcrumb = []}) {
  return (
    <div id="subheader" style={{height: "104px", backgroundColor: siteConfig.themeSubheaderColor, color: siteConfig.themeSubheaderTextColor}}>
        <div className="flex items-center ml-auto mr-auto" style={{maxWidth: "1140px", paddingTop: "15px"}}>
          {
            breadcrumb.map((item, index) => {
              if (item.length == 3) {
                return (<span key={index} id="breadcrumb" className="pr-2" style={{color: siteConfig.themeBreadcrumbColor}}> 
                    <Link style={{color: siteConfig.themeBreadcrumbColor}} href={route(item[1], item[2])}>{item[0]}</Link>
                    </span>);                              
              } else { // length == 2
                return (<span key={index} id="breadcrumb" className="pr-2" style={{color: siteConfig.themeBreadcrumbColor}}>
                  <Link style={{color: siteConfig.themeBreadcrumbColor}} href={route(item[1])}>{item[0]}</Link>
                  </span>);
              }
            })
          }
        </div>
      
        <div className="items-center ml-auto mr-auto" 
          style={{maxWidth: "1140px", paddingTop: "15px", marginTop: "15px", fontSize: "24px", fontWeight: "900"}}>
        {text}
      </div>
    </div>
  );
}