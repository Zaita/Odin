import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import TextField from '@/Components/TextField';
import TextAreaField from '@/Components/TextAreaField';
import ThemedButton from '@/Components/ThemedButton';
import DropdownField from '@/Components/DropdownField';

import { SaveAnswers, SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function EditPillars(props) {  
  console.log("Admin.Content.SecurityCatalogue.Add");  
  let [saveErrors, setSaveErrors] = useState("");
  let [saveOk, setSaveOk] = useState(null);
  let userAnswers = useRef([]);

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }

  let nameField = { 
    "label" : "Name",
    "placeholder": "",
    "required": true,
    "value": userAnswers.current["name"] ? userAnswers.current["name"] : props.catalogue.name,
  }

  let descriptionField = { 
    "label": "Description",
    "placeholder": "",
    "required": false,
    "value": userAnswers.current["description"] ? userAnswers.current["description"] : props.catalogue.description,
  }
 
  function saveAnswersCallback() {
    SaveAnswersWithId("admin.content.securitycatalogue.save", props.catalogue.id, setSaveOk, setSaveErrors, userAnswers.current)
  }

  function MyContent() {
    return (
      <>
      <div className="flex">
        <div className="overflow-y-auto w-5/6">
          {/* Title */}
          <div className="w-full">
          <TextField field={nameField} value={nameField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} camalCase runInit/>
          </div>
          {/* Description */}
          <div className="w-full">
          <TextField field={descriptionField} value={descriptionField.value} submitCallback={saveAnswersCallback}
                handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} height="65px" camalCase runInit/>
          </div>                    
        </div>
      </div>
      <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
        <div className="float-left w-auto inline-block" ><ThemedButton siteConfig={props.siteConfig} onClick={saveAnswersCallback} children="Save"/></div>
        <div className="pl-2 font-bold">{saveOk}</div>
      </div> 
      </>
    );
  }

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"],
    [props.catalogue.name, "admin.content.securitycatalogue.edit", props.catalogue.id]
  ]

  let topMenuItems = [
    ["Catalogue", "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<MyContent props/>}/>
  );
}
