import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function SecurityCatalogue_Add(props) {  
  let nameField = { 
    "label" : "Name",
    "type" : "textfield",
    "required": true,
  }

  let descriptionField = { 
    "label" : "Description",
    "type" : "textfield",
    "required": false,
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(descriptionField);
  
  let myContent = <Admin_AddScreen {...props} inputFields={inputFields} 
  createRoute="admin.content.securitycatalogue.create"
  title="Add New Security Catalogue"/>

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
