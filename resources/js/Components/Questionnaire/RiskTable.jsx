export default function Questionnaire_RiskTable(props) { 
  // This is not configured for displaying a risk table
  if (props.submission.type == "questionnaire" || props.submission.risk_calculation == "none") {
    return <></>
  }

  let riskData = JSON.parse(props.submission.risk_data);
  let output = [];
  riskData.map((risk, index) => output.push(
    <div key={index} className="mt-1"
      style={{
        backgroundColor: props.siteConfig.theme_input_bg_color,
        color: props.siteConfig.theme_input_text_color
        }}>
      <div className="inline-block w-3/12 p-2">{risk["name"]}</div>
      <div className="inline-block w-2/12 p-2"
        style={{backgroundColor: risk["color"]}}>
          {risk["rating"]}
          {risk["showScore"] && " (" + risk["score"] + ")"}
      </div>
      <div className="inline-block w-7/12 p-2">{risk["description"]}</div>       
    </div>) );

  return (
    <div className="mt-2 mb-2">
      <span className="text-lg font-bold">{props.riskTitle}</span>
      <div className="p-2" 
        style={{
        backgroundColor: props.siteConfig.theme_input_bg_color,
        color: props.siteConfig.theme_input_text_color
        }}>
          <div className="inline-block w-3/12 font-bold">Risk</div>
          <div className="inline-block w-2/12 font-bold">Rating</div>
          <div className="inline-block w-7/12 font-bold">Description</div>
      </div>      
      {output}
    </div>
  );
}