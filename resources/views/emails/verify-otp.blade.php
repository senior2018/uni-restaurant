@component('mail::message')
# Hello {{ $name }} 👋

Thank you for registering with **{{ config('app.name') }}**!

To complete your registration, please verify your email using the 6-digit One-Time Password (OTP) below:

@component('mail::panel')
## 🔐 Your OTP Code: **{{ $otp }}**
@endcomponent

This code will expire in 10 minutes.

If you didn't request this, you can safely ignore this email.

Thanks,<br>
**The {{ config('app.name') }} Team**

@slot('subcopy')
Having trouble? Contact support at [support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}](mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }})
@endslot
@endcomponent
