@component('mail::message')
# Welcome {{$user->name}}

Thanks for creating an account.
You can verify your account by clicking the link below:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
