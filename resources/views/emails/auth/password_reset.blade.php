<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Protocol</title>
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
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d5a27 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
        }
        .content {
            padding: 40px;
        }
        .greeting {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1a4d2e;
        }
        .instruction {
            color: #475569;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .cta-box {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-top: 40px;
        }
        .cta-button {
            display: inline-block;
            background-color: #D4A373; /* agri-earth */
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3);
        }
        .expiry-warning {
            margin-top: 25px;
            padding: 15px;
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            color: #92400e;
            font-size: 13px;
            font-weight: 600;
            border-radius: 4px;
        }
        .footer {
            padding: 30px;
            background-color: #f8fafc;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: white">IAM</h1>
            <p style="color: #e9c46a; font-weight: 700; margin-top: 10px; text-transform: uppercase; letter-spacing: 2px;">INTERNATIONAL AGRO MARKETPLACE</p>
        </div>
        
        <div class="content">
            <div class="greeting">Hello, {{ $user->name }}</div>
            <p class="instruction">We received a request to change your password for your International Agro Marketplace (IAM) account.</p>
            
            <p>Click the button below to set a new password:</p>

            <div class="cta-box">
                <center>
                    <a href="{{ $resetUrl }}" class="cta-button">Set New Password</a>
                </center>
                <div class="expiry-warning">
                    This link will expire in 60 minutes.
                </div>
            </div>

            <p style="margin-top: 40px; font-size: 14px; color: #64748b;">If you didn't ask to change your password, you can safely ignore this email. Your account is still secure.</p>
        </div>

        <div class="footer">
            &copy; 2026 INTERNATIONAL AGRO MARKETPLACE (IAM).<br>
            Connecting Farmers and Buyers Globally.
        </div>
    </div>
</body>
</html>
