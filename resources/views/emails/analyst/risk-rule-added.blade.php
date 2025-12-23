<x-mail::message>
# New Risk Rule Assigned

Your analyst has assigned a new risk compliance rule to your account.

**Rule Type:** {{ ucfirst(str_replace('_', ' ', $rule->rule_type)) }} <br>
**Value:** {{ $rule->value ?? 'N/A' }} <br>
**Enforcement:** {{ $rule->is_hard_stop ? 'Hard Stop (Enforced)' : 'Soft Warning' }}

Please review your trading plan to ensure compliance.

<x-mail::button :url="route('trader.dashboard')">
View Rules
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
