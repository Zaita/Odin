import React, {useState, forwardRef, useRef } from 'react';
import { useImperativeHandle } from 'react';

import CircleIcon from '@mui/icons-material/Circle';

const DSRA_RiskTable = forwardRef(function DSRA_RiskTable(props, ref) {
  let initialRisk = JSON.parse(props.initial_risk.risk_data);

  let [renderFlag, setRenderFlag] = useState(false);
  let likelihoodPenalties = useRef(calculateLikelihoodPenalties());
  let impactPenalties = useRef(calculateImpactPenalties());

  function calculateLikelihoodPenalties() {
    let penalties = {};
    initialRisk.map(risk => penalties[risk.name] = 0);

    props.controls.map(control => {
      riskWeights = JSON.parse(control.risk_weights);
      riskWeights.map(weight => penalties[weight.risk.name] += likelihood_penalty);      
    })
  }

  function calculateImpactPenalties() {
    let penalties = {};
    initialRisk.map(risk => penalties[risk.name] = 0);

    props.controls.map(control => {
      riskWeights = JSON.parse(control.risk_weights);
      riskWeights.map(weight => penalties[weight.risk.name] += impact_penalty);      
    })
  }





  let initialRisk = JSON.parse(props.initial_risk.risk_data);

  useImperativeHandle(ref, () => ({
    updateTable() {
      setRenderFlag(!renderFlag);
    }
  }));

  function currentRiskRating(risk) {
    return <div className="w-2/12 inline-block">Critical</div>
  }

  function Likelihood(risk) {
    

    return <>
      <div className="w-2/12 inline-block">
        <CircleIcon style={{width: "10px", height: "10px", color: risk.color}} className="mr-2"/>
        {risk.rating}
      </div>
      <div className="w-2/12 inline-block">{risk.score} / 100</div>
    </>
  }

  function Impact(riskname) {
    return <>
      <div className="w-2/12 inline-block">Extreme</div>
      <div className="w-2/12 inline-block">240.24 / 273</div>
    </>
  }
  
  return (<div ref={ref} style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
    <div className="w-2/12 inline-block font-bold">Risk name</div>
    <div className="w-2/12 inline-block font-bold">Current risk rating</div>
    <div className="w-4/12 inline-block font-bold">Likelihood score</div>
    <div className="w-4/12 inline-block font-bold">Impact Score</div>

    {initialRisk.map((risk, key) => 
      <span key={key}>
      <div className="w-2/12 inline-block">{risk.name}</div> 
      {currentRiskRating(risk)}
      {Likelihood(risk)}
      {Impact(risk)}
      </span>
    )}
  </div>)
});

export default DSRA_RiskTable;