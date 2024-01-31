import React from 'react';

import AdminPanel from '@/Layouts/AdminPanel';

export default function Home(props) {
  function MyContent() {
    return (
      <>Admin Home</>
    );
  }

  let breadcrumb = [
    ["Home", "admin.home"],        
  ];

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent/>}/>
  );
}