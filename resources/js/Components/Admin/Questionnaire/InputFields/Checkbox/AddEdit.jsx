import React, { useRef, useState } from 'react';
import ReportIcon from '@mui/icons-material/Report';

import SimpleTextField from '@/Components/SimpleTextField';
import TextField from "@/Components/TextField";
import ThemedButton from "@/Components/ThemedButton";
import { SaveAnswersWithId } from '@/Components/Admin/SaveAnswers';

export default function InputCheckBoxAddEdit(props) {
  let [renderFlag, setRenderFlag] = useState(false);
  let [saveOk, setSaveOk] = useState([]);
  let [errors, setErrors] = useState([]);
  let userAnswers = useRef([]);

  function getValue(targetProp, label) {
    if (userAnswers.current[label] == null) 
      userAnswers.current[label] = targetProp;
    return userAnswers.current[label] ? userAnswers.current[label] : (targetProp ?  targetProp : "");
  }

  function checkError(risk, field) {
    if (risk + "||" + field in props.errors) {
      return (<p id="error" style={{color: props.siteConfig.theme_error_text_color}}>
          <ReportIcon/> {props.errors[risk + "||" + field]}
        </p>)
    }

    return <></>;
  }

  let risks = JSON.parse(props.option.risks);  
  function getRiskValue(riskName, field) {

    if (risks && risks[riskName] && risks[riskName][field]) {
      return risks[riskName][field];
    }

    return "0";
  }

  function handleChange(id, value) {
    userAnswers.current[id] = value;
  }
  
  function saveCallback() {
    SaveAnswersWithId(props.saveRoute, props.routeParameters, setSaveOk, setErrors, userAnswers.current);
  }

  let labelField = {
    "label" : "Label",
    "value" : getValue(props.option.label, "label")
  }
  let valueField = {
    "label" : "Value",
    "value" : getValue(props.option.value, "value")
  }

  return (
    <div className="pt-1 pb-2">
      <div>
        <div className="inline-block w-3/5">
          <div className="inline-block w-10/12">
            <TextField field={labelField} value={labelField.value} submitCallback={saveCallback}
                      handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide/>
          </div>
          <div className="inline-block w-10/12">
            <TextField field={valueField} value={valueField.value} submitCallback={saveCallback}
                      handleChange={handleChange} errors={errors} siteConfig={props.siteConfig} camalCase sideBySide/>
          </div> 
          <div className="inline-block w-1/2">
            <div>
              <div className="inline-block w-4/12 font-bold">Risk name</div>
              <div className="inline-block w-2/12 font-bold">Impact</div>              
            </div>
            <div>
              {props.risks.map((risk, rIndex) => <div key={"risk" + rIndex} className="mb-1 mt-1">
                <div className="inline-block w-4/12">{risk.name}</div>
                <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "||impact"} value={getRiskValue(risk.name, "impact")} 
                    submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} runInit/>
                </div>
                <div>
                  {checkError(risk.name, "impact")}
                </div>
              </div>)}
            </div>
          </div> 
          <div className="inline-block w-1/3 align-top">
          <div className="inline-block font-bold">Impact Thresholds</div>
          {
            props.thresholds?.map((threshold, index) => 
            <div key={"threshold_" + index} className="p-2 mt-1"
              style={{backgroundColor: threshold.color}}>
              A value {threshold.operator}{threshold.value} is {threshold.name}
            </div>
            )
          }
          </div>
        </div>
      </div>
      <div className="pt-1">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/><p>{saveOk}</p>
      </div>
    </div>
  );
}