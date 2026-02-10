@component('mail::message')
# Dispute Filed

A new dispute has been filed against your account.

**Subscription:** {{ $dispute->subscription->plan }} Plan
**Filed By:** {{ $dispute->trader->name }}
**Reason:** {{ ucwords(str_replace('_', ' ', $dispute->reason)) }}

**Description:**
{{ $dispute->description }}

An admin will review this case shortly. You may be contacted for further information.

@component('mail::button', ['url' => route('login')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
