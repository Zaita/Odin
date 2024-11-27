import { Link } from "@inertiajs/react"

export default function TopMenu({siteConfig, topMenuItems, breadcrumb, selected=""}) {
  function MenuBox(item, index) {
    let menuButtonStyle = {
      borderColor: siteConfig.theme_admin_topmenu_item_border_color, 
      color: siteConfig.theme_admin_topmenu_item_text_color
    };
    if (route().current() == item[1]) {
      menuButtonStyle = {
        borderColor: siteConfig.theme_admin_topmenu_item_border_color, 
        color: siteConfig.theme_admin_topmenu_item_text_color,
        textDecoration: 'underline',
      };
    }

    if (item.length == 3) {
      return (<div key={index} className="min-w-24 table-cell text-center border-l-2 pl-1 pr-1" style={menuButtonStyle}>
          <Link href={route(item[1], item[2])}>{item[0]}</Link></div>)
    }
    // length == 2
    return (<div key={index} className="min-w-24 table-cell text-center border-l-2 pl-1 pr-1" style={menuButtonStyle}>
        <Link href={route(item[1])}>{item[0]}</Link></div>)                  
  }

  return (
    <div id="top_menu" className="w-full h-10 border-b"
    style={{backgroundColor: siteConfig.theme_admin_topmenu_bg_color, 
      color: siteConfig.theme_admin_topmenu_text_color,
      borderColor: siteConfig.theme_admin_topmenu_border_color}}>
      <div className="flex mr-auto">
        <div className="flex pl-2" style={{paddingTop: "10px"}}>
        {
          breadcrumb.map((item, index) => {
            if (item.length == 3) {
              return (<span key={index} id="breadcrumb" className="pr-2"
                style={{color: siteConfig.theme_admin_topmenu_breadcrumb_color}}>
                  <Link href={route(item[1], item[2])}>{item[0]}</Link></span>);                              
            } else { // length == 2
              return (<span key={index} id="breadcrumb" className="pr-2"
              style={{color: siteConfig.theme_admin_topmenu_breadcrumb_color}}>
                  <Link href={route(item[1])}>{item[0]}</Link></span>);
            }
          })
        }
        </div>
        <div className="flex ml-auto" style={{paddingTop: "10px"}}>
          <div className="table">
          {topMenuItems.map && topMenuItems.map((item, index) => MenuBox(item, index))}                 
          </div>
        </div>
      </div>
    </div>
  )
}
