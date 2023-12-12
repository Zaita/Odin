import Menu from "@/Components/Admin/Menu";
import TopMenu from "@/Components/Admin/TopMenu";
import ActionMenu from "@/Components/Admin/ActionMenu";

export default function AdminPanel({auth, siteConfig, topMenuItems=[], actionMenuItems=[], breadcrumb=[], content = "<div></div>"}) {

  let actionMenu = actionMenuItems.map ? <ActionMenu siteConfig={siteConfig} actionMenuItems={actionMenuItems}/> : "";

  return(
    <div id="adminPanel" className='h-screen w-screen text-xs'>   
      <div className="flex h-full">
        <Menu user={auth.user} siteConfig={siteConfig}/>
        <div className="block w-full">
          <TopMenu siteConfig={siteConfig} topMenuItems={topMenuItems} breadcrumb={breadcrumb}/>
          {actionMenu}
          <div className="pt-2 pl-2 pb-2 pr-2">{content}</div>
        </div>
      </div>      
    </div>
  );
}