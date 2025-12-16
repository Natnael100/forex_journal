<x-mail::message>
# New Feedback Received 💬

Hi {{ $trader->name }},

You have received new feedback on your trade from **{{ $feedback->analyst->name }}**.

Reviewing this feedback is crucial for improving your performance.

<x-mail::button :url="route('trader.feedback.show', $feedback->id)">
View Feedback
</x-mail::button>

Keep growing,  
The PipJournal Team
</x-mail::message>
