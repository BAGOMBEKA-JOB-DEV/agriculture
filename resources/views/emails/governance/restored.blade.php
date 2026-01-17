<x-mail::message>
# Account Access Restored

Dear {{ $user->name }},

We are happy to let you know that your access to the **International Agro Marketplace (IAM)** has been restored.

Your account is now fully active, and you can resume trading, managing your products, and updating your profile.

<x-mail::panel>
**Status: Fully Restored**
</x-mail::panel>

You can now sign in to your account and continue your trade activities.

<x-mail::button :url="config('app.url') . '/login'">
Go to Marketplace
</x-mail::button>

Thanks,<br>
The IAM Team<br>
{{ config('app.name') }}
</x-mail::message>
