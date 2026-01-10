<x-mail::message>
# Identity Restoration: Access Protocols Re-established

Dear {{ $user->name }},

We are pleased to inform you that your profile has been successfully audited and your access to the **Governance Nexus: Global Trade Hub** has been fully restored.

The suspension on your account has been lifted, and your node is once again active in the global registry. You may now:
- Access all marketplace discovery suites.
- Manage your trade inventory and RFQs.
- Initiate sovereign settlements.

<x-mail::panel>
**Status Update: Restoration Complete**
Node State: Active
Privileges: Full Participant Access
</x-mail::panel>

Welcome back to the global trade floor. Please log in to synchronize your workspace.

<x-mail::button :url="config('app.url') . '/login'">
Access Trade Floor
</x-mail::button>

Regards,<br>
Governance Nexus Administration<br>
{{ config('app.name') }}
</x-mail::message>
