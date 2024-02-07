import React, { useRef, useState } from 'react';

import AdminPanel from '@/Layouts/AdminPanel';
import Admin_AddScreen from '@/Components/Admin/Admin.AddScreen';

export default function Security_User_Add(props) {  
  let nameField = { 
    "label" : "Name",
    "type": "textfield",
    "placeholder": "Full name",
    "required": true,
  }

  let emailField = { 
    "label": "Email",
    "type": "textfield",
    "placeholder": "name@example.com",
    "required": true,
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

  let myContent = <Admin_AddScreen {...props} inputFields={inputFields} 
  createRoute="admin.security.user.create"
  title="Add New User"/>

  let breadcrumb = [
    ["Users", "admin.security.users"]
  ]

  return (
    <AdminPanel {...props} topMenuItems={[]} actionMenuItems={[]} breadcrumb={breadcrumb} content={myContent}/>
  );
}
