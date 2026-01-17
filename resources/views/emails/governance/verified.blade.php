<x-mail::message>
# Account Verified

Dear {{ $user->name }},

We have finished checking your documents, and your account on the **International Agro Marketplace (IAM)** is now **Verified**.

A verified account helps you build trust with other users and makes your profile stand out.

<x-mail::panel>
**Status: Verified Participant**  
Network Visibility: Global Tier
</x-mail::panel>

Thank you for being part of our safe and trusted marketplace.

<x-mail::button :url="config('app.url') . '/dashboard'">
View Your Profile
</x-mail::button>

Best regards,<br>
The IAM Team<br>
{{ config('app.name') }}
</x-mail::message>
