<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f7f6; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #1a4d2e; padding: 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
        .content { padding: 40px; text-align: center; }
        .greeting { font-size: 20px; font-weight: 700; color: #1a4d2e; margin-bottom: 20px; }
        .instruction { font-size: 16px; color: #64748b; margin-bottom: 30px; }
        .code-box { background-color: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px; margin: 30px 0; }
        .code { font-size: 48px; font-weight: 800; color: #1a4d2e; letter-spacing: 10px; margin: 0; }
        .footer { padding: 30px; background-color: #f8fafc; text-align: center; font-size: 13px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .expiry { font-size: 12px; color: #94a3b8; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>IAM</h1>
            <p style="color: #e9c46a; font-weight: 700; margin-top: 5px; text-transform: uppercase; letter-spacing: 2px; font-size: 11px;">INTERNATIONAL AGRO MARKETPLACE</p>
        </div>
        
        <div class="content">
            <div class="greeting">Security Verification</div>
            <p class="instruction">Use the code below to finish signing in to your International Agro Marketplace (IAM) account.</p>
            
            <div class="code-box">
                <p class="code">{{ $code }}</p>
            </div>

            <p class="expiry">This code will expire in 10 minutes. If you didn't try to sign in, please change your password immediately.</p>
        </div>

        <div class="footer">
            &copy; 2026 INTERNATIONAL AGRO MARKETPLACE (IAM).<br>
            Connecting Farmers and Buyers Globally.
        </div>
    </div>
</body>
</html>
