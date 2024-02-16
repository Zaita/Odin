
import CheckBoxOutlineBlankIcon from '@mui/icons-material/CheckBoxOutlineBlank';
import EditIcon from '@mui/icons-material/Edit';
import CheckBoxIcon from '@mui/icons-material/CheckBox';
import BlockIcon from '@mui/icons-material/Block';

export default function Questionnaire_Menu({callBack, ...props }) {
  /**
   * Build our initial list of menu items
   */
  let menuItems = [];
  props.questions.map((question, index) => {
    // Array is [<Icon>, title, have hyperlink? ]
    // Default with box icon
    menuItems.push([<CheckBoxOutlineBlankIcon style={{width: "14px"}}/>, question.title, false]);
  })

  /**
   * Re-iterate over our items and modify them based
   * on whether or not it's the current question, or we hav an answer
   * supplied from the backend
   */
  props.questions.map((question, index) => {
    if (props.currentQuestion && question.title == props.currentQuestion.title) {
      // Is this the current question?
      menuItems[index][0] = <EditIcon style={{width: "14px"}}/>; 
    } else if (props.answers && props.answers.answers) {
      // Is this a question that we have an answer for?
      props.answers.answers.map((answer, index) => {
        if (answer.question == question.title) {
          if (answer.status == "complete") {
            // Change icon to a green checkbox and add hyperlink
            menuItems[index][0] = <CheckBoxIcon style={{width: "14px", color: "green"}}/>;
            menuItems[index][2] = true; // Can be navigated to
          } else if (answer.status == "not_applicable") {
            // Change icon a a blocked out (not applicable) sign
            menuItems[index][0] = <BlockIcon style={{width: "14px"}}/>;
          } 
        }
      });      
    }
  });

  return (      
    <div id="menu" style={{backgroundColor: props.siteConfig.theme_content_bg_color}}>
      <div id="items">        
        {menuItems.map && menuItems.map((item, idx) => {
          if (item[2]) {
            return(<div key={idx} className="leftBarItem"><div className="cursor-pointer" onClick={() => callBack(item[1])}>{item[0]} {idx+1}. {item[1]}</div></div>)
          } else {
            return(<div key={idx} className="leftBarItem"><div className="cursor-pointer">{item[0]} {idx+1}. {item[1]}</div></div>)
          }
        }
        )}
      </div>      
    </div>
  );
}