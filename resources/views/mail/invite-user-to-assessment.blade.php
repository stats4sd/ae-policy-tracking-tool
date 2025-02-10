@component('mail::message')

{{ $invite->inviter->name }} ({{ $invite->inviter->email }}) has invited you to register on the online Agroecology Policy Assessment tool to collaborate on the {{ $invite->team->title }} assessment.

Click the link below to register on the platform. Please use the same email address that received this email, so that your new account can be assigned the correct permissions. Once you have registered, you may change your email address through the profile management page.

<x-mail::button :url='$acceptUrl'>
Register here.
</x-mail::button>

If you do not wish to register, or you have been sent this email by mistake, please ignore this message.


Thanks,<br>
{{ config('app.name') }}

@endcomponent
