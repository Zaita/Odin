import React from 'react';
import AdminPanel from '@/Layouts/AdminPanel';
import Admin_EditScreen from '@/Components/Admin/Admin.EditScreen';

export default function Security_User_Edit(props) {  
  let nameField = { 
    "label" : "Name",
    "type": "textfield",
    "placeholder": "Full name",
    "required": true,
    "value" : props.user.name,
  }

  let emailField = { 
    "label": "Email",
    "type": "textfield",
    "placeholder": "name@example.com",
    "required": true,
    "value" : props.user.email,
  }

  let passwordField = { 
    "label": "Password",
    "type": "textfield",
    "placeholder": "****",
    "required": false,
  }

  let inputFields = [];
  inputFields.push(nameField);
  inputFields.push(emailField);
  inputFields.push(passwordField);

  let myContent = <Admin_EditScreen {...props} inputFields={inputFields} 
  saveRoute="admin.security.user.save"
  saveRouteParameters={props.user.id}
  title="Modify Existing User"/>

  let breadcrumb = [
    ["Users", "admin.security.users"],
    [props.user.name, "admin.security.user.edit", props.user.id]
  ]
  
  let topMenuItems = [
    ["User", "admin.security.user.edit", props.user.id],
    ["Groups", "admin.security.user.groups", props.user.id]
  ]

  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
