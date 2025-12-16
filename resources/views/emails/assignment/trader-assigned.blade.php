<x-mail::message>
# Analyst Assigned 🔗

Hi {{ $trader->name }},

**{{ $analyst->name }}** has been assigned as your performance analyst.

They will be reviewing your trades and providing detailed feedback to help you improve your strategies.

<x-mail::button :url="route('analyst.trader.profile', $analyst->id)">
View Analyst Profile
</x-mail::button>

Happy Trading,  
The PipJournal Team
</x-mail::message>
