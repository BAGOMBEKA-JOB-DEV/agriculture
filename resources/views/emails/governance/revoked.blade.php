<x-mail::message>
# Account Access Update

Dear {{ $user->name }},

We are writing to let you know that your access to the **International Agro Marketplace (IAM)** has been suspended.

This was done to comply with our security and marketplace rules. While your account is suspended:
- You cannot access the trading floor.
- Your products are no longer visible to others.
- You cannot make any changes to your profile.

<x-mail::panel>
**Status: Suspended**
</x-mail::panel>

If you think this is a mistake or need help getting back in, please contact our support team.

<x-mail::button :url="config('app.url') . '/login'">
Contact Support
</x-mail::button>

Thanks,<br>
The IAM Team<br>
{{ config('app.name') }}
</x-mail::message>
