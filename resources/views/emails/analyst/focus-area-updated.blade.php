<x-mail::message>
# Coaching Focus Updated

Your analyst has updated your coaching focus area for Guided Journaling.

**New Focus:** {{ ucfirst($assignment->current_focus_area) }}

Your trade entry journal will now prioritize questions related to this area.

<x-mail::button :url="route('trader.trades.create')">
Start Journaling
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
