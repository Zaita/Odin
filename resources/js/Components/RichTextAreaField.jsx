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

export default function RichTextAreaField(props) {
  const [value, setValue] = useState({
    html: props.value,
    editable: true
  });

  const blocksFromHtml = htmlToDraft(props.value);
  const { contentBlocks, entityMap } = blocksFromHtml;
  const contentState = ContentState.createFromBlockArray(contentBlocks, entityMap);
  const [editorState, setEditorState] = useState(EditorState.createWithContent(contentState));

  let fieldId = props.camalCase ? camalCase(props.field.label) : props.field.label;
  fieldId = props.dbFormat ? dbFormat(fieldId) : fieldId;
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
    const rawContentState = convertToRaw(editorState.getCurrentContent());
    const markup = draftToHtml(rawContentState);
    props.handleChange(fieldId, markup);
  })

  return (
    <div id="input_field">
      <div id="label"><label htmlFor={fieldId}>{label}</label></div>
      <div className="w-2/3 bg-white overflow-auto" style={{height: "400px"}}>
        <Editor
          editorState={editorState}
          toolbarClassName="toolbarClassName"
          wrapperClassName="wrapperClassName"
          editorClassName="editorClassName"
          onEditorStateChange={handleChange}         
        />
        <p id="error" style={{color: props.siteConfig.themeSubheaderColor}}>{error}</p> 
      </div>
    </div>
  )
}