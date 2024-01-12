import UserLayout from '@/Layouts/UserLayout';

export default function Approvals(props) {
  function Content(props) {
    return (
      <div id="content_box" className="mt-5">
        Approvals
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Approvals", "approvals"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Approvals" subheaderText="My Approvals" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}