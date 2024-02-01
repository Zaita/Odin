import { useState} from 'react';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';

export default function ThemedButton({ className = '', disabled, siteConfig,  children, ...props }) {  
  const [hover, setHover] = useState(false);

  function handleOnClick() {
    if (props.handleChange) {
      props.handleChange("action", `${children}`);
    }

    props.onClick();
  }

  let icon = children == "Next" ? <ChevronRightIcon/> : null;
  return (
      <button
          onMouseEnter={()=>{
            setHover(true);
          }}
          onMouseLeave={()=>{
            setHover(false);
          }}

          onClick={() => handleOnClick()}
          
          className={                
              `inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest00 focus:outline-none transition ease-in-out duration-150 
              ${disabled && 'opacity-25'} ` + className
          }

          style={{
            backgroundColor: (hover || props.selected ? siteConfig.theme_btn_hover_bg_color : siteConfig.theme_btn_bg_color),
            color: (hover || props.selected ? siteConfig.theme_btn_hover_text_color : siteConfig.theme_btn_text_color),
            borderStyle: "solid",
            borderWidth: "2px",
            borderColor: (hover || props.selected ? siteConfig.theme_btn_hover_text_color : siteConfig.theme_btn_text_color),
          }}

          autoFocus={props.autofocus}
          disabled={disabled}
      >
          {children}
          {icon}
      </button>
  );
}
