import { Link } from "@inertiajs/react"

export default function ActionMenu({siteConfig, actionMenuItems}) {
    let content = actionMenuItems?.length > 0 ? 
    (
    <div id="action_menu" className="h-10 pt-1">
      <div className="table">
      {actionMenuItems.map && actionMenuItems.map((item, index) => {
        return (<div key={index} className="pl-2 pr-4 table-cell cursor-pointer">{item}</div>)
      })}                 
      </div>
    </div>
    ) : "";

    return (
      <>
      {content}
      </>
    )
}
