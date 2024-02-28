import React, {useState, forwardRef, useRef } from 'react';
import { useImperativeHandle } from 'react';

import CircleIcon from '@mui/icons-material/Circle';

const DSRA_RiskTable = forwardRef(function DSRA_RiskTable(props, ref) {
  let controls = useRef(props.controls);
  let initialRisk = JSON.parse(props.initial_risk.risk_data);

  let [renderFlag, setRenderFlag] = useState(false);
  let maxLikelihoodPenalties = useRef(null);
  let maxImpactPenalties = useRef(null);
  let likelihoodMultiplier = useRef({});
  let impactMultiplier = useRef({});
  
  /**
   * Create a callback method for the parent to pass in new information about
   * when a control changes it's implementation status
   */
  useImperativeHandle(ref, () => ({
    updateTable(controlId, newImplementationStatus) {
      controls.current.map(control => {
        if (control.id == controlId) {
          control.implementation_status = newImplementationStatus;
        }
      });

      setRenderFlag(!renderFlag);
    }
  }));

  /**
   * We need to do a bit of prep work on the first load.
   * 1. calculate the maximum amount of penalties that can be applied
   * 2. sum the total weights for each risk
   * 3. create a multiplier for each risk to use with the weights
   */
  // if (maxLikelihoodPenalties.current == null || maxImpactPenalties.current == null) {
    let likelihoodPenalties = {};
    let impactPenalties = {};
    let likelihoodSum = {};
    let impactSum = {};
    // init some arrays
    initialRisk.map(risk => {
      likelihoodPenalties[risk.name] = 0;
      impactPenalties[risk.name] = 0;
      likelihoodSum[risk.name] = 0;
      impactSum[risk.name] = 0;
      likelihoodMultiplier.current[risk.name] = 0;
      impactMultiplier.current[risk.name] = 0;
    });

    // calculate maximum penalties and sum the total weights
    props.controls.map(control => {
      let controlRisks = JSON.parse(control.risk_weights);
      Object.entries(controlRisks).map(([riskName, weightArray]) => {
        if (control.implementation_status != "not_applicable") {
          likelihoodPenalties[riskName] += weightArray["likelihood_penalty"];
          impactPenalties[riskName] += weightArray["impact_penalty"];
          likelihoodSum[riskName] += weightArray["likelihood_weight"];
          impactSum[riskName] += weightArray["impact_weight"];
        }
      });      
    })

    // Calculate the weight multiplier
    initialRisk.map(risk => {
      likelihoodMultiplier.current[risk.name]  = 100 / Math.max(likelihoodSum[risk.name], 1);
      impactMultiplier.current[risk.name]  = risk.score  / Math.max(impactSum[risk.name], 1);
    });
    
    // assign the maximum penalties to the objects
    maxLikelihoodPenalties.current = likelihoodPenalties;
    maxImpactPenalties.current = impactPenalties;
  // }

  /**
   * Calculate our scores
   */
  let likelihoodScores = {};
  let impactScores = {};
  initialRisk.map((risk) => {
    // calculate the initial score
    likelihoodScores[risk.name] = 100 + maxLikelihoodPenalties.current[risk.name];
    impactScores[risk.name] = risk.score + maxImpactPenalties.current[risk.name];
    controls.current.map((control) => {      
      let controlRisks = JSON.parse(control.risk_weights);
      if (controlRisks == undefined || controlRisks[risk.name] == undefined) {
        return;
      }
      let likelihoodValue = controlRisks[risk.name]["likelihood_weight"] * likelihoodMultiplier.current[risk.name];
      let impactValue = controlRisks[risk.name]["impact_weight"] * impactMultiplier.current[risk.name];
      let likelihoodPenalty = controlRisks[risk.name]["likelihood_penalty"];
      let impactPenalty = controlRisks[risk.name]["impact_penalty"];

      if (control.implementation_status == "planned" || control.implementation_status == "implemented") {        
        likelihoodScores[risk.name] -= (likelihoodValue + likelihoodPenalty);
        impactScores[risk.name] -= (impactValue + impactPenalty);
      }
    });
  });


  /**
   * 
   * @param {*} risk 
   * @returns 
   */
  function currentRiskRating(risk) {
    return <div className="w-2/12 inline-block p-2 pl-4 m-0" style={{backgroundColor: risk.color}}>Critical</div>
  }

  /**
   * Return our Likelihood information block
   */
  function Likelihood(risk) {
    return (
      <div className="w-3/12 inline-block m-0">
        <div className="w-32 inline-block p-2 pl-4 m-0">
          <CircleIcon style={{width: "10px", height: "10px", color: risk.color}} className="mr-2"/>
          {risk.rating}
        </div>
        <div className="inline-block pr-4 w-20">
          <span className="font-bold">{Math.abs(likelihoodScores[risk.name].toFixed(0))}</span> / {100 + maxLikelihoodPenalties.current[risk.name]}</div>
        {/* <div className="inline-block bg-green-300 p-1 font-bold">-100</div> */}
      </div>);
  }

  function Impact(risk) {
    return (
      <div className="w-3/12 inline-block m-0">
        <div className="w-32 inline-block p-2 pl-4 m-0">
          <CircleIcon style={{width: "10px", height: "10px", color: risk.color}} className="mr-2"/>
          Extreme
        </div>
        <div className="inline-block pr-4 w-20"><span className="font-bold">{Math.abs(impactScores[risk.name].toFixed(0))}</span> / {risk.score + maxImpactPenalties.current[risk.name]}</div>
        {/* <div className="inline-block bg-pink-300 p-1 font-bold">+100</div> */}
      </div>);
  }
  
  return (<div ref={ref}>
    <div className="mb-1" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
      <div className="w-4/12 inline-block font-bold p-2 pl-4">Risk name</div>
      <div className="w-2/12 inline-block font-bold p-2 pl-4">Current risk rating</div>
      <div className="w-3/12 inline-block font-bold p-2 pl-4">Likelihood score</div>
      <div className="w-3/12 inline-block font-bold p-2 pl-4">Impact Score</div>
    </div>

    {initialRisk.map((risk, key) => 
      <div key={key} className="p-0 mb-1" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
      <div className="w-4/12 inline-block p-2 pl-4">{risk.name}</div> 
      {currentRiskRating(risk)}
      {Likelihood(risk)}
      {Impact(risk)}
      </div>
    )}
  </div>)
});

export default DSRA_RiskTable;