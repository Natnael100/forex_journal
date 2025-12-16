<x-mail::message>
# Account Verified 🎉

Hi {{ $user->name }},

Congratulations! Your account has been verified by our team. You now have full access to PipJournal, including dashboard analytics and feedback requests.

<x-mail::button :url="route('login')">
Go to Dashboard
</x-mail::button>

Happy Trading,  
The PipJournal Team
</x-mail::message>
