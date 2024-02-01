import React, { useRef, useState } from 'react';
import SimpleTextField from '@/Components/SimpleTextField';
import TextField from "@/Components/TextField";
import ThemedButton from "@/Components/ThemedButton";

export default function InputCheckBoxAddEdit(props) {
  let [renderFlag, setRenderFlag] = useState(false);
  let [errors, saveErrors] = useState(false);
  let options = useRef([]);

  function handleChange(id, value) {
  
  }
  
  function saveCallback() {

  }
  let field = {
    "label" : "Label",
    "value" : ""
  }
  let field2 = {
    "label" : "Value",
    "value" : ""
  }

  return (
    <div className="pt-1 pb-2">
      <div>
        <div className="inline-block w-3/5">
          <div className="inline-block w-10/12">
          <TextField field={field} value={field.value} submitCallback={saveCallback}
                    handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>
          <div className="inline-block w-10/12">
          <TextField field={field2} value={field2.value} submitCallback={saveCallback}
                    handleChange={handleChange} errors={saveErrors} siteConfig={props.siteConfig} dbFormat sideBySide/>
          </div>          
          <div>
            <div className="inline-block w-4/12 font-bold">Risk name</div>
            <div className="inline-block w-2/12 font-bold">Likelihood</div>
            <div className="inline-block w-2/12 font-bold">Likelihood penalty</div>
            <div className="inline-block w-2/12 font-bold">Impact</div>
            <div className="inline-block w-2/12 font-bold">Impact penalty</div>
          </div>
          <div>
            {props.risks.map((risk, rIndex) => <div key={"risk" + rIndex} className="mb-1 mt-1">
              <div className="inline-block w-4/12">{risk.name}</div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "_likelihood"} value={risk.likelihood} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} dbFormat sideBySide/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "_likelihood"} value={risk.likelihood} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} dbFormat sideBySide/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "_likelihood"} value={risk.likelihood} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} dbFormat sideBySide/>
              </div>
              <div className="inline-block w-2/12"><SimpleTextField label={risk.name + "_likelihood"} value={risk.likelihood} 
                  submitCallback={saveCallback} handleChange={handleChange} siteConfig={props.siteConfig} dbFormat sideBySide/>
              </div>
            </div>)}
          </div>
        </div>
      </div>
      <div className="pt-1">
        <ThemedButton siteConfig={props.siteConfig} onClick={saveCallback} children="Save"/>
      </div>
    </div>
  );
}