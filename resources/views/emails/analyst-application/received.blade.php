<x-mail::message>
# Application Received

Hi {{ $application->name }},

Thanks for applying to join pipJournal as a Performance Analyst!

We have successfully received your application. Our team will review your credentials, experience, and application details over the coming days.

### What happens next?
1. **Review**: We verify your certifications and track record.
2. **Decision**: You'll receive an email with our decision typically within 2-3 business days.
3. **Onboarding**: If approved, you'll get a link to set up your account.

<x-mail::button :url="route('login')">
Return to pipJournal
</x-mail::button>

Thanks,<br>
The pipJournal Team
</x-mail::message>
