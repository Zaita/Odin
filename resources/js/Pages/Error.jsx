import UserLayout from '@/Layouts/UserLayout';

export default function Error(props) {
  function Content(props) {
    return (
      <div id="content_box" className="mt-5">
        <pre>An error has been encountered with your request:</pre>
        <pre>Error: {props.errors?.error}</pre>
      </div>
    )
  }
  let breadcrumb = [
    ["Home", "home"],
    ["Error", "error"]    
  ]

  return (
    <UserLayout siteConfig={props.siteConfig} selectedMenu="Home" subheaderText="Error" 
      breadcrumb={breadcrumb}
      content={<Content {...props}/>} />
  );
}
