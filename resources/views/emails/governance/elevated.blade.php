<x-mail::message>
# Super Admin Access Granted

Dear {{ $user->name }},

You have been granted **Super Admin** access to the **International Agro Marketplace (IAM)**.

As a Super Admin, you can manage the entire marketplace, including users, products, and system settings. Please use this access responsibly.

<x-mail::panel>
**Role: Super Admin**  
Access: Full System Management
</x-mail::panel>

You can now access your admin dashboard to begin.

<x-mail::button :url="config('app.url') . '/super-admin'">
Go to Admin Dashboard
</x-mail::button>

Thanks,<br>
The IAM Team<br>
{{ config('app.name') }}
</x-mail::message>
