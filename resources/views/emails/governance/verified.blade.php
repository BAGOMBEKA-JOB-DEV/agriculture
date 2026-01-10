<x-mail::message>
# Identity Authorization: Registry Verification Complete

Dear {{ $user->name }},

Following a comprehensive audit of your documentation, the **Governance Nexus** has successfully authorized your identity.

Your participant profile is now marked as **Verified** within the Global Trade Registry. This authorization enhances your node's reputation and visibility across the network, signaling sovereign compliance to all trade partners.

<x-mail::panel>
**Authorization Type: Advanced Identity Verification**
Registry Token: Verified Participant
Network Visibility: Global Tier
</x-mail::panel>

Thank you for maintaining high compliance standards in our marketplace.

<x-mail::button :url="config('app.url') . '/dashboard'">
View Verified Profile
</x-mail::button>

Best regards,<br>
Sovereign Identity Bureau<br>
{{ config('app.name') }}
</x-mail::message>
