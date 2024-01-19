import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function Guest(props) {
  return (
    <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style={{backgroundColor: props.siteConfig.themeLoginBgColor}}>
      <div className="w-full sm:max-w-md py-2 sm:rounded-t-lg items-center" style={{backgroundColor: props.siteConfig.themeHeaderColor}}>
        <Link href="/">
          <img src={props.siteConfig.logoPath} className="fill-current" />
        </Link>
      </div>

      <div className="w-full sm:max-w-md px-6 py-4 shadow-md overflow-hidden sm:rounded-b-lg"
        style={{backgroundColor: "#ffffff"}}>
        {props.children}
      </div>
    </div>
  );
}
