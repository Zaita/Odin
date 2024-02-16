import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function SecurityCatalogue_Edit(props) {  
  console.log("Admin.Content.SecurityCatalogue.Edit");  

  let nameField = { 
    "label" : "Name",
    "type" : "textfield",
    "required": true,
    "value": props.catalogue.name,
  }

  let descriptionField = { 
    "label" : "Description",
    "type" : "textfield",
    "required": false,
    "value": props.catalogue.description,
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(descriptionField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
    saveRoute="admin.content.securitycatalogue.save"
    saveRouteParameters={props.catalogue.id}
    title="Modify Security Catalogue"/>

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"],
    [props.catalogue.name, "admin.content.securitycatalogue.edit", props.catalogue.id]
  ]

  let topMenuItems = [
    ["Catalogue", "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
