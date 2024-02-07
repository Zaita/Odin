import React, { useRef, useState, Component } from 'react';
import { router } from '@inertiajs/react'

import AdminPanel from '@/Layouts/AdminPanel';
import ThemedButton from '@/Components/ThemedButton';
import SimpleTextField from '@/Components/SimpleTextField';

import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';
import Admin_TextField from '@/Components/Admin/Inputs/Admin.TextField';
import Admin_RichTextAreaField from '@/Components/Admin/Inputs/Admin.RichTextAreaField';

export default function SecurityCatalogue_Controls(props) {  
  let [saveOk, setSaveOk] = useState([]);
  let [errors, setErrors] = useState([]);
  let userAnswers = useRef([]);

  function checkError(risk, field) {
    if (risk + "||" + field in props.errors) {
      return (<p id="error" style={{color: props.siteConfig.theme_error_text_color}}>
          <ReportIcon/> {props.errors[risk + "||" + field]}
        </p>)
    }

    return <></>;
  }

  function getRiskValue(riskName, field) {
    let lookup = riskName + "||" + field;
    return "0";
  }

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
  
  function saveCallback() {
    SaveAnswersWithId("admin.content.securitycontrol.create", props.catalogue.id, setSaveOk, setErrors, userAnswers.current);
  }

  let nameField = {
    "label" : "Name",
    "value" : userAnswers.current["name"] ? userAnswers.current["name"] : "",
    "required": true,
  }
  let descriptionField = {
    "label" : "Description",
    "value" : userAnswers.current["description"] ? userAnswers.current["description"] : "",
    "required": true,
  }
  let implementationGuidanceField = {
    "label" : "Implementation guidance",
    "value" : userAnswers.current["implementation_guidance"] ? userAnswers.current["implementation_guidance"] : "",
  }  
  let implementationEvidenceField = {
    "label" : "Implementation evidence",
    "value" : userAnswers.current["implementation_evidence"] ? userAnswers.current["implementation_evidence"] : "",
  } 
  let auditGuidanceField = {
    "label" : "Audit guidance",
    "value" : userAnswers.current["audit_guidance"] ? userAnswers.current["audit_guidance"] : "",
  } 
  let referenceStandardsField = {
    "label" : "Reference standards",
    "value" : userAnswers.current["reference_standards"] ? userAnswers.current["reference_standards"] : "",
  }
  let controlOwnerNameField = {
    "label" : "Control owner name",
    "value" : userAnswers.current["control_owner_name"] ? userAnswers.current["control_owner_name"] : "",
  }
  let controlOwnerEmailField = {
    "label" : "Control owner email",
    "value" : userAnswers.current["control_owner_email"] ? userAnswers.current["control_owner_email"] : "",
  }

  let inputProps = {handleChange, submitCallBack:saveCallback, errors, siteConfig:props.siteConfig, dbFormat:true, sideBySide:true};

  function Content() {
    return (
      <div className="pt-1 pb-2">
        <div className="font-bold">Add New Control</div>
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
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||likelihood"} value={getRiskValue(risk.name, "likelihood")} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||likelihood_penalty"} value={getRiskValue(risk.name, "likelihood_penalty")} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||impact"} value={getRiskValue(risk.name, "impact")} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||impact_penalty"} value={getRiskValue(risk.name, "impact_penalty")} 
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
        <div className="pt-1">
          <ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Create"/><p>{saveOk}</p>
        </div>
      </div>
    );
  }

  let breadcrumb = [
    ["Security Catalogues", "admin.content.securitycatalogues"],
    [props.catalogue.name, "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Security Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  let topMenuItems = [
    ["Catalogue", "admin.content.securitycatalogue.edit", props.catalogue.id],
    ["Controls", "admin.content.securitycatalogue.controls", props.catalogue.id]
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={<Content/>}/>
  );
}
