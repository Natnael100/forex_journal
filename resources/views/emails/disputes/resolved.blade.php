@component('mail::message')
# Dispute Resolved

Your dispute (Case #{{ $dispute->id }}) has been resolved.

**Resolution:** {{ ucfirst($dispute->resolution) }}

**Admin Notes:**
{{ $dispute->admin_notes }}

@if($dispute->resolution === 'refund')
A refund has been processed for your subscription.
@endif

@component('mail::button', ['url' => route('trader.disputes.show', $dispute->id)])
View Case Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
