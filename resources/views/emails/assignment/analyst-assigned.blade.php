<x-mail::message>
# New Trader Assignment 🔗

Hi {{ $analyst->name }},

You have been assigned a new trader: **{{ $trader->name }}**.

Please review their profile and recent trades to start providing guidance.

<x-mail::button :url="route('analyst.trader.profile', $trader->id)">
View Trader Profile
</x-mail::button>

Best regards,  
The PipJournal Team
</x-mail::message>
