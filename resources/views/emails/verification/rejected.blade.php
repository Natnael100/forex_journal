<x-mail::message>
# Verification Update

Hi {{ $user->name }},

Upon reviewing your profile, we could not verify your account at this time.

**Reason:**
{{ $reason }}

Please log in to update your profile information and resubmit for verification.

<x-mail::button :url="route('profile.edit')">
Update Profile
</x-mail::button>

Best regards,  
The PipJournal Team
</x-mail::message>
