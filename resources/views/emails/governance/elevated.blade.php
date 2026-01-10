<x-mail::message>
# Authority Protocol: Sovereign Elevation Authorized

Dear {{ $user->name }},

This formal notification confirms your elevation to the rank of **Super Admin** within the **Governance Nexus**.

You have been granted system-wide sovereign access, enabling full oversight and management of the marketplace's regulatory framework, participant registries, and trade asset classes. Please exercise this authority with the highest standards of governance.

<x-mail::panel>
**Protocol Level: Sovereign Authority**
Account Role: Super Admin
Access Scope: System-Wide Unrestricted
</x-mail::panel>

Your new administrative capabilities are now active in your dashboard.

<x-mail::button :url="config('app.url') . '/super-admin'">
Enter Governance Nexus
</x-mail::button>

Issued by,<br>
Central Administration Board<br>
{{ config('app.name') }}
</x-mail::message>
