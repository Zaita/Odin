import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'
import ReportIcon from '@mui/icons-material/Report';

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import SimpleTextField from '@/Components/SimpleTextField';
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import Admin_RichTextAreaField from '@/Components/Admin/Inputs/Admin.RichTextAreaField';

export default function SecurityCatalogue_Control_Edit(props) {  
  let [saveOk, setSaveOk] = useState(props.saveOk);
  let [errors, setErrors] = useState([]);
  let userAnswers = useRef([]);
  let error = props.errors && "save" in props.errors ? (<><ReportIcon/> {props.errors["save"]}</>) : "";

  function riskValue(riskName, field) {
    for (let index = 0; index < props.control.risk_weights.length; index++) {
      const element = props.control.risk_weights[index];
      if (element.risk.name == riskName) {
        switch(field) {
          case "likelihood":
            return element.likelihood;
          case "likelihood_penalty":
            return element.likelihood_penalty;
          case "impact":
            return element.impact;
          case "impact_penaalty":
            return element.impact_penalty;
        }
      }
    }
   
    return "0";
  }

  function checkError(risk, field) {
    if (risk + "||" + field in props.errors) {
      return (<p id="error" style={{color: props.siteConfig.theme_error_text_color}}>
          <ReportIcon/> {props.errors[risk + "||" + field]}
        </p>)
    }

    return <></>;
  }

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
  
  function saveCallback() {
    SaveAnswersWithId("admin.content.securitycontrol.save", {id:props.catalogue.id, controlId:props.control.id}, setSaveOk, setErrors, userAnswers.current);
  }

  let nameField = {
    "label" : "Name",
    "value" : props.control.name,
    "required": true,
  }
  let descriptionField = {
    "label" : "Description",
    "value" : props.control.description,
    "required": true,
  }
  let implementationGuidanceField = {
    "label" : "Implementation guidance",
    "value" : props.control.implementation_guidance,
  }  
  let implementationEvidenceField = {
    "label" : "Implementation evidence",
    "value" : props.control.implementation_evidence
  } 
  let auditGuidanceField = {
    "label" : "Audit guidance",
    "value" : props.control.audit_guidance
  } 
  let referenceStandardsField = {
    "label" : "Reference standards",
    "value" : props.control.reference_standards
  }
  let controlOwnerNameField = {
    "label" : "Control owner name",
    "value" : props.control.control_owner_name
  }
  let controlOwnerEmailField = {
    "label" : "Control owner email",
    "value" : props.control.control_owner_email
  }

  let inputProps = {handleChange, submitCallBack:saveCallback, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true, runInit:true };



  function Content() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Modify Control</div>
        <div className="inline-block w-11/12">
          <Admin_TextField field={nameField} {...inputProps}/>
          <Admin_RichTextAreaField field={descriptionField} {...inputProps}/>
          <Admin_RichTextAreaField field={implementationGuidanceField} {...inputProps}/>
          <Admin_RichTextAreaField field={implementationEvidenceField} {...inputProps}/>
          <Admin_RichTextAreaField field={auditGuidanceField} {...inputProps}/>
          <Admin_TextField field={referenceStandardsField} {...inputProps}/>
          <Admin_TextField field={controlOwnerNameField} {...inputProps}/>
          <Admin_TextField field={controlOwnerEmailField} {...inputProps}/>
          <div>
            <div className="inline-block w-48 font-bold">Risk name</div>
            <div className="inline-block w-2/12 font-bold">Likelihood</div>
            <div className="inline-block w-2/12 font-bold">Likelihood penalty</div>
            <div className="inline-block w-2/12 font-bold">Impact</div>
            <div className="inline-block w-2/12 font-bold">Impact penalty</div>
          </div>
          <div>
            {props.risks.map((risk, rIndex) => <div key={"risk" + rIndex} className="mb-1 mt-1">
              <div className="inline-block w-48">{risk.name}</div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||likelihood"} value={riskValue(risk.name, "likelihood")}
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||likelihood_penalty"} value={riskValue(risk.name, "likelihood_penalty")}
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||impact"} value={riskValue(risk.name, "impact")}
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||impact_penalty"} value={riskValue(risk.name, "impact_penalty")}
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div>
                {checkError(risk.name, "likelihood")}
                {checkError(risk.name, "likelihood_penalty")}
                {checkError(risk.name, "impact")}
                {checkError(risk.name, "impact_penalty")}                
              </div>
            </div>)}
          </div>
        </div>
        <div id="bottom_menu" className="flex h-10 border-t-2 border-solid border-white pt-2">
          <div className="float-left w-auto inline-block" >
              <ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/>
              <ThemedButton siteConfig={props.siteConfig} onClick={() => router.get(route('admin.content.securitycontrol.add', props.catalogue.id))} children="Add New Control"/>,
            </div>


          <div className="pl-2 font-bold">{saveOk}</div>
          <div className="pl-2 font-bold" style={{color: props.siteConfig.theme_error_text_color}}>{error}</div>
        </div> 
      </div>
    );
  }

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"],
    [props.catalogue.name, "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Security Controls", "admin.content.securitycatalogue.controls", props.catalogue.id],
    [props.control.name, "admin.content.securitycontrol.edit", {id:props.catalogue.id, controlId:props.control.id}]
  ]

  let topMenuItems = [
    ["Catalogue", "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<Content/>}/>
  );
}
