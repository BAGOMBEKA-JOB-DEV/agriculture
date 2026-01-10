<x-mail::message>
# Sovereign Access Revocation

Dear {{ $user->name }},

We are writing to formally notify you that your access to the **Governance Nexus: Global Trade Hub** has been suspended.

This action has been taken by the Sovereign Administration in accordance with marketplace protocol and security compliance requirements. During this period:
- Your access to the trading floor is restricted.
- All active listings have been moved to a pending classification.
- Profile authorization has been temporarily locked.

<x-mail::panel>
**Protocol ID: #REVOKED**
Status: Suspended
Jurisdiction: Nexus Compliance Registry
</x-mail::panel>

If you believe this action was taken in error or if you wish to provide additional documentation for identity verification, please contact our senior administrative support team.

<x-mail::button :url="config('app.url') . '/login'">
Contact Support
</x-mail::button>

Sincerely,<br>
Sovereign Administration Office<br>
{{ config('app.name') }}
</x-mail::message>
