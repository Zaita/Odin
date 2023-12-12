import AdminPanel from "@/Layouts/AdminPanel";

function Content() {
  return (
    <div>
      Security.jsx Content
    </div>
  );
}


export default function Security({ auth, siteConfig }) {
    return (
      <AdminPanel auth={auth} siteConfig={siteConfig} content={<Content/>} />      
    );
}
