<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Alert</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #1a4d2e; /* agri-forest */
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px;
        }
        .content h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a4d2e;
        }
        .alert-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }
        .alert-item {
            margin-bottom: 15px;
            display: flex;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 10px;
        }
        .alert-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-weight: 700;
            width: 120px;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .value {
            flex: 1;
            font-weight: 500;
            color: #1e293b;
        }
        .footer {
            padding: 30px;
            background-color: #f8fafc;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .cta-button {
            display: inline-block;
            background-color: #D4A373; /* agri-earth */
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            margin-top: 20px;
        }
        .warning-text {
            color: #e53e3e;
            font-weight: 600;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Governance Nexus</h1>
        </div>
        <div class="content">
            <h2>New Login Detected</h2>
            <p>Hi {{ $user->name }},</p>
            <p>We noticed a new login to your account. To help keep your account secure, we're letting you know about this activity.</p>
            
            <div class="alert-box">
                <div class="alert-item">
                    <div class="label">Date & Time</div>
                    <div class="value">{{ $time }}</div>
                </div>
                <div class="alert-item">
                    <div class="label">IP Address</div>
                    <div class="value">{{ $ipAddress }}</div>
                </div>
                <div class="alert-item">
                    <div class="label">Device Info</div>
                    <div class="value">{{ $userAgent }}</div>
                </div>
            </div>

            <p class="warning-text">Wasn't this you?</p>
            <p>If you don't recognize this activity, please change your password immediately and contact our security team.</p>
            
            <center>
                <a href="#" class="cta-button">Report Unauthorized Access</a>
            </center>

            <p style="margin-top: 40px; font-size: 13px; color: #94a3b8;">
                If you have any questions, feel free to call our support line at: <strong>+1 (800) AGRI-SAFE</strong> or email us at <strong>security@agrimarketplace.io</strong>
            </p>
        </div>
        <div class="footer">
            &copy; 2026 International Agriculture Marketplace. All rights reserved.<br>
            Secure Identity Protocol • IAM Center
        </div>
    </div>
</body>
</html>
