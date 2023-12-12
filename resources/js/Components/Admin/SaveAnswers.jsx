import React, { useRef, useState } from 'react';
import { router } from '@inertiajs/react'

export function SaveAnswers(routeName, successCallback, errorCallback, userData) {
  router.visit(route(routeName), {
      method: "post",
      preserveScroll: true,
      preserveState: true,
      data: {
        ...userData
      },
      onSuccess: (page) => {
        console.log("Saved Successfully");
        successCallback("Saved Successfully");
        errorCallback(null);
      },
      onError: (errors) => {
        console.log("saveAnswers Failed");            
        successCallback(null);
        errorCallback(errors);
      },
  })
}

export function SaveAnswersWithId(routeName, id, successCallback, errorCallback, userData) {
  router.visit(route(routeName, id), {
      method: "post",
      preserveScroll: true,
      preserveState: true,
      headers: {
        'Content-Type': 'application/json',
        'Accepts': 'application/json',
      },
      data: {
        ...userData
      },
      onSuccess: (page) => {
        console.log("Saved Successfully");
        successCallback("Saved Successfully");
        errorCallback(null);
      },
      onError: (errors) => {
        console.log("saveAnswers Failed");            
        successCallback(null);
        errorCallback(errors);
      },
  })
}