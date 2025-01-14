import { useEffect } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import ThemedButton from '@/Components/ThemedButton';
import TextInput from '@/Components/TextInput';
import TextField from '@/Components/TextField';
import { Head, Link, router, useForm } from '@inertiajs/react';

export default function Login(props) {
  let status = props.status;
  let canResetPassword = props.canResetPassword;

  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('login'));
  };

  return (
    <GuestLayout {...props}>
      <Head title="Log in" />

      {status && <div className="mb-4 font-medium text-sm text-green-600">{status}</div>}

      <form onSubmit={submit}>
        <div>
          <InputLabel htmlFor="email" value="Email" style={{color: props.siteConfig.theme_text_color}} />

          <TextInput
            id="email"
            type="email"
            name="email"
            value={data.email}
            className="mt-1 block w-full"
            autoComplete="username"
            isFocused={true}
            onChange={(e) => setData('email', e.target.value)}
            style={{
              backgroundColor: "#FFFFFF",
              borderColor: props.siteConfig.theme_header_color,
              color: props.siteConfig.theme_text_color
            }}
          />

          <InputError message={errors.email} className="mt-2" />
        </div>

        <div className="mt-4">
          <InputLabel htmlFor="password" value="Password" style={{color: props.siteConfig.theme_text_color}} />

          <TextInput
            id="password"
            type="password"
            name="password"
            value={data.password}
            className="mt-1 block w-full"
            autoComplete="current-password"
            onChange={(e) => setData('password', e.target.value)}
            style={{
              backgroundColor: "#FFFFFF",
              borderColor: props.siteConfig.theme_header_color,
              color: props.siteConfig.theme_text_color
            }}
          />

          <InputError message={errors.password} className="mt-2" />
        </div>

        {false && (<div className="block mt-4">
          <label className="flex items-center">
            <Checkbox
              name="remember"
              checked={data.remember}
              onChange={(e) => setData('remember', e.target.checked)}
              style={{
                backgroundColor: "#FFFFFF",
                borderColor: props.siteConfig.theme_header_color,
                color: props.siteConfig.theme_text_color
              }}
            />
            <span className="ms-2 text-sm">Remember me</span>
          </label>
        </div>)}

        <div className="flex items-center justify-end mt-4">
          {canResetPassword && (
            <Link
              href={route('password.request')}
              className="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
              style={{color: props.siteConfig.theme_header_color }}
            >
              Forgot your password?
            </Link>
          )}

          <PrimaryButton className="ms-4" disabled={processing}
            style={{
              backgroundColor: props.siteConfig.theme_header_color,
              color: props.siteConfig.theme_header_text_color,
              borderStyle: "solid",
              borderWidth: "2px",
              borderColor: props.siteConfig.theme_header_color
            }}>
            Log in
          </PrimaryButton>
        </div>        
      </form>
      <div className="flex items-center">
        <ThemedButton siteConfig={props.siteConfig} onClick={() => {window.location.href=route("login.okta")}} children="Login with Okta"/>       
      </div>
    </GuestLayout>
  );
}
