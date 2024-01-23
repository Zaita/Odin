import { Link } from "@inertiajs/react"

export default function TopMenu({topMenuItems, breadcrumb, selected=""}) {
    return (
      <div id="top_menu" className="bg-basedarkred text-white w-full h-10">
        <div className="flex mr-auto">
          <div className="flex" style={{paddingTop: "10px"}}>
          {
            breadcrumb.map((item, index) => {
              if (item.length == 3) {
                return (<span key={index} id="breadcrumb" className="pr-2"><Link href={route(item[1], item[2])}>{item[0]}</Link></span>);                              
              } else { // length == 2
                return (<span key={index} id="breadcrumb" className="pr-2"><Link href={route(item[1])}>{item[0]}</Link></span>);
              }
            })
          }
          </div>
          <div className="flex ml-auto" style={{paddingTop: "10px"}}>
            <div className="table">
            {topMenuItems.map && topMenuItems.map((item, index) => {
              if (item.length == 3) {
                return (<div key={index} className="min-w-24 table-cell text-center border-l-2 pl-1 pr-1"><Link href={route(item[1], item[2])}>{item[0]}</Link></div>)
              } else { // length == 2
                return (<div key={index} className="min-w-24 table-cell text-center border-l-2 pl-1 pr-1"><Link href={route(item[1])}>{item[0]}</Link></div>)              
              }
            })}                 
            </div>
          </div>
        </div>
      </div>
    )
}
