import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';

import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function EditPillars(props) {  
  console.log("Admin.Content.SecurityCatalogue.Add");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

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
