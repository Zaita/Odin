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
            backgroundColor: (hover || props.selected ? siteConfig.themeHeaderColor : siteConfig.themeHeaderTextColor),
            color: (hover || props.selected ? siteConfig.themeHeaderTextColor : siteConfig.themeHeaderColor),
            borderStyle: "solid",
            borderWidth: "2px",
            borderColor: siteConfig.themeHeaderColor
          }}

          autoFocus={props.autofocus}
          disabled={disabled}
      >
          {children}
          {icon}
      </button>
  );
}
