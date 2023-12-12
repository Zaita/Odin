import React, { useRef, useState, Component } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';

const menu = [
  { title : "Home", 
    pages : [
      { title : "Reports" },
      { title : "Audit Log"}
    ]
  },
  {
    title : "Maintenance",
    pages: [
      { title : "Site Configuration" },
      { title : "Email Configuration"}
    ]
  }

];

class RightPanel extends React.Component {
  render() {
    // var output = JSON.parse(menu);
    // var output = menu.map((item) => <div>{item}</div>);    
    return (
      <section>
      Home
      </section>      
    )}  
}


export default function Home({ auth, siteConfig }) {
    return (
      <AdminPanel auth={auth} siteConfig={siteConfig} content={<RightPanel/>}/>        
    );
}
