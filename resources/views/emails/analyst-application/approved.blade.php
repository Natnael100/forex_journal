<x-mail::message>
# Welcome to pipJournal!

Congratulations, {{ $user->name }}! 🎉

Your application to become a Performance Analyst has been **APPROVED**. We were impressed by your experience and credentials.

You can now set up your password and access your analyst dashboard.

<x-mail::button :url="route('password.reset', ['token' => $token, 'email' => $user->email])">
Set Your Password
</x-mail::button>

### Next Steps:
1. Set your password using the button above.
2. Complete your analyst profile (add a bio, profile photo, and cover photo).
3. Set up your subscription plans so traders can subscribe to you.

We're excited to have you on board!

Thanks,<br>
The pipJournal Team
</x-mail::message>
