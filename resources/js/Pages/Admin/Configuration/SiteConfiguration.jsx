import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link, useForm, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import React, { useRef,useState, Component } from 'react';

import TextField from '@/Components/TextField';
import AdminPanel from '@/Layouts/AdminPanel';

function SiteConfigPanel(props) {
  // const user = usePage().props.auth.user;

  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
      title: props.siteConfig.title,
      footerText: props.siteConfig.footerText,
      alternateEmail: props.siteConfig.alternateEmail,
      securityTeamEmail: props.siteConfig.securityTeamEmail,
  });

  const submit = (e) => {
      e.preventDefault();
      patch(route('admin.configuration.siteconfig.update'));
  };

  let titleField = { 
    "label" : "Title",
    "placeholder": "",
    "required": true,
    "value": props.siteConfig.title
  }

  return (
    <>
    <div className="flex">
      <div className="overflow-y-auto">
        <form onSubmit={submit}>
          <div className="w-full">
            <TextField field={titleField} value="" submitCallback={submit}
                handleChange={(a, b) => {}} errors={[]} siteConfig={props.siteConfig} camalCase/>
            <InputLabel htmlFor="Title" value="Title" className="w-72 float-left border-2 border-blue-700 mt-3"/>
            <TextInput
                id="title"
                className="mt-1"
                value={data.title}
                onChange={(e) => setData('title', e.target.value)}
                required
                isFocused
                autoComplete="title"
            />
            <InputError className="mt-2" message={errors.title} />
          </div>

          <div className="w-full">
              <InputLabel htmlFor="footerText" value="Footer Text" className="w-72 float-left border-2 border-blue-700 mt-3"/>
              <TextInput
                  id="footerText"
                  className="mt-1"
                  value={data.footerText}
                  onChange={(e) => setData('footerText', e.target.value)}
                  required
                  autoComplete="footerText"
              />
              <InputError className="mt-2" message={errors.footerText} />
          </div>

            {/* Alernate Email Address */}
          <div className="w-full">
              <InputLabel htmlFor="alternateEmail" value="Alternate Email Address" className="w-72 float-left border-2 border-blue-700 mt-3"/>
              <TextInput
                  id="alternateEmail"
                  className="mt-1"
                  value={data.alternateEmail}
                  onChange={(e) => setData('alternateEmail', e.target.value)}
                  autoComplete="alternateEmail"
              />
              <InputError className="mt-2" message={errors.alternateEmail} />
          </div>

          <div className="w-full">
            <InputLabel htmlFor="securityTeamEmail" value="Security Team Email" className="w-72 float-left border-2 border-blue-700 mt-3"/>
            <TextInput
                id="securityTeamEmail"
                className="mt-1"
                value={data.securityTeamEmail}
                onChange={(e) => setData('securityTeamEmail', e.target.value)}
                required
                autoComplete="securityTeamEmail"
            />
            <InputError className="mt-2" message={errors.securityTeamEmail} />
        </div>                        

        <div className="flex items-center gap-4">
            <PrimaryButton disabled={processing}>Save</PrimaryButton>

            <Transition
                show={recentlySuccessful}
                enter="transition ease-in-out"
                enterFrom="opacity-0"
                leave="transition ease-in-out"
                leaveTo="opacity-0"
            >
                <p className="text-sm text-gray-600 dark:text-gray-400">Saved.</p>
            </Transition>
        </div>
        </form>
        </div>
    </div>
    </>
);
}

let topMenuItems = [
  [ "Dashboard", "admin.content.dashboard"],
  [ "Pillars", "admin.content.dashboard.pillars"],
  [ "Tasks", "admin.content.dashboard.tasks"]
]

export default function SiteConfiguration(props) {
  return (
    <AdminPanel {...props} topMenuItems={topMenuItems} content={<SiteConfigPanel {...props}/>}/>
  );
}
