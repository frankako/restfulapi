@component('mail::message')
# Welcome {{$user->name}}

You have changed your account email.
Verify your new email by clicking the link below:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
