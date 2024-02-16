import { useState, useEffect } from 'react';
import ReportIcon from '@mui/icons-material/Report';
import { EditorState, ContentState } from 'draft-js';
import { Editor } from "react-draft-wysiwyg";
import "react-draft-wysiwyg/dist/react-draft-wysiwyg.css";
import { convertToRaw } from 'draft-js';
import draftToHtml from 'draftjs-to-html'; // https://github.com/jpuri/draftjs-to-html
import htmlToDraft from 'html-to-draftjs';

import camalCase from "@/Utilities/camal.jsx"
import dbFormat from '@/Utilities/dbFormat';

export default function Admin_RichTextAreaField(props) {
  const [isFocused, setIsFocused] = useState(false);
  const [value, setValue] = useState({
    html: props.value,
    editable: true
  });

  const blocksFromHtml = htmlToDraft(props.field.value ? props.field.value : "");
  const { contentBlocks, entityMap } = blocksFromHtml;
  const contentState = ContentState.createFromBlockArray(contentBlocks, entityMap);
  const [editorState, setEditorState] = useState(EditorState.createWithContent(contentState));

  let fieldId = props.camalCase ? camalCase(props.field.label) : dbFormat(props.field.label);
  let label = props.field.required ? (<>{props.field.label} *</>) : props.field.label;  
  let error = props.errors && fieldId in props.errors ? (<><ReportIcon/> {props.errors[fieldId]}</>) : "";

  /**
   * Handle the change in the text field locally
   * so we can store the value in the parent object
   * for submitting to the back end. This is needed because
   * our form is completely user-defined.
   * @param {event} e 
   */
  function handleChange(newState) {
    setEditorState(newState);
    const rawContentState = convertToRaw(newState.getCurrentContent());
    const markup = draftToHtml(rawContentState);
    props.handleChange(fieldId, markup);
  }

  useEffect(() => {
    // Re-Populate after a fresh load/render
    // This is because programatically changing field value
    // in a form error won't trigger onChange
    if (editorState.getCurrentContent() != null && editorState.getCurrentContent() != "") {
      const rawContentState = convertToRaw(editorState.getCurrentContent());
      const markup = draftToHtml(rawContentState);
      props.handleChange(fieldId, markup);
    }
  })

  return (
    <div id="input_field" className="pb-2">
      <div id="label" className="float-left inline-block w-48">
        <label htmlFor={fieldId}>{label}</label>
      </div>
      <div className="inline-block w-auto max-w-5xl" 
        style={{borderColor: props.siteConfig.theme_input_border_color}}
        >
        <Editor
          editorState={editorState}
          toolbarClassName="toolbarClassName"
          wrapperClassName="wrapperClassName"
          editorClassName="editorClassName"
          onEditorStateChange={handleChange}   
          editorStyle={{ 
            backgroundColor: props.siteConfig.theme_input_bg_color,
            color: props.siteConfig.theme_input_text_color,
            borderColor: props.siteConfig.theme_input_border_color, 
            borderWidth: "0px 1px 1px 1px",  
            boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none",                                               
            marginTop: "0px",
            }}
          toolbarStyle={{ 
            backgroundColor: props.siteConfig.theme_input_bg_color,
            color: props.siteConfig.theme_input_text_color,
            borderColor: props.siteConfig.theme_input_border_color, 
            borderWidth: "1px 1px 0px 1px",  
            marginBottom: "0px",
            boxShadow: isFocused ? "1px 1px 0px 2px " + props.siteConfig.theme_input_border_color : "none",                                               
            }}
          onBlur={() => setIsFocused(false)}
          onFocus={() => setIsFocused(true)}            
          />
        </div>
        <p id="error" style={{color: props.siteConfig.theme_error_text_color}}>{error}</p> 
    </div>
  )
}