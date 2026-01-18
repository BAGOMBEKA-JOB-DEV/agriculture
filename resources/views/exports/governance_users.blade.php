<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sovereign Participant Registry - Export</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1a1a1a; margin: 40px; }
        .header { border-bottom: 2px solid #166534; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 24px; font-weight: bold; color: #166534; }
        .meta { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f9fafb; padding: 12px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        td { padding: 12px 10px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-active { background: #dcfce7; color: #166534; }
        .badge-suspended { background: #fee2e2; color: #991b1b; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #9ca3af; padding: 20px 0; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Sovereign Participant Registry</div>
        <div class="meta">Governance Protocol Export | Generated on {{ now()->format('d M Y, H:i') }} (UTC)</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Participant Identity</th>
                <th>Jurisdiction</th>
                <th>Authority Role</th>
                <th>Status</th>
                <th>Initialization</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>#{{ $user->id }}</td>
                <td>
                    <strong>{{ $user->name }}</strong><br>
                    <span style="color: #6b7280;">{{ $user->email }}</span>
                </td>
                <td>{{ $user->location ?: 'Global Repository' }}</td>
                <td>{{ ucfirst($user->role ?: ($user->roles->first()?->name ?: 'N/A')) }}</td>
                <td>
                    <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                        {{ $user->status }}
                    </span>
                </td>
                <td>{{ $user->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        IAM Marketplace Governance Authority | Sovereign Data Integrity Protocol
    </div>
</body>
</html>
