<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Marketplace</title>
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
        .showcase {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin: 30px 0;
        }
        .product-card {
            text-align: center;
        }
        .product-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #edf2f7;
        }
        .product-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            margin-top: 8px;
            letter-spacing: 1px;
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
            <div class="greeting">Welcome, {{ $user->name }}!</div>
            <p>You are now part of the International Agro Marketplace (IAM). We connect farmers, buyers, and traders from all over the world.</p>
            
            <p style="font-weight: 600; color: #475569; margin-top: 30px;">What you can find on IAM:</p>
            <div class="showcase">
                <div class="product-card">
                    <div style="width: 100%; height: 120px; background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%); border-radius: 8px; border: 1px solid #edf2f7; display: flex; align-items: center; justify-content: center;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L12 22M12 2C10 2 8 4 8 6L8 18C8 20 10 22 12 22M12 2C14 2 16 4 16 6L16 18C16 20 14 22 12 22M9 8C8 8 7 9 7 10C7 11 8 12 9 12M15 8C16 8 17 9 17 10C17 11 16 12 15 12M9 14C8 14 7 15 7 16C7 17 8 18 9 18M15 14C16 14 17 15 17 16C17 17 16 18 15 18" stroke="#d97706" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="product-label">Golden Maize</div>
                </div>
                <div class="product-card">
                    <div style="width: 100%; height: 120px; background: linear-gradient(135deg, #451a03 0%, #78350f 100%); border-radius: 8px; border: 1px solid #edf2f7; display: flex; align-items: center; justify-content: center;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="12" cy="12" rx="4" ry="6" fill="#a16207" opacity="0.8"/>
                            <ellipse cx="8" cy="10" rx="3" ry="5" fill="#92400e" opacity="0.9"/>
                            <ellipse cx="16" cy="14" rx="3" ry="5" fill="#92400e" opacity="0.9"/>
                            <circle cx="12" cy="8" r="2" fill="#d97706"/>
                            <circle cx="10" cy="14" r="2" fill="#b45309"/>
                            <circle cx="14" cy="11" r="2" fill="#b45309"/>
                        </svg>
                    </div>
                    <div class="product-label">Premium Coffee</div>
                </div>
                <div class="product-card">
                    <div style="width: 100%; height: 120px; background: linear-gradient(135deg, #f5f5dc 0%, #d2b48c 100%); border-radius: 8px; border: 1px solid #edf2f7; display: flex; align-items: center; justify-content: center;">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C12 2 10 6 10 10C10 12 11 14 12 14C13 14 14 12 14 10C14 6 12 2 12 2Z" fill="#8b4513" opacity="0.7"/>
                            <ellipse cx="12" cy="16" rx="5" ry="3" fill="#a0522d"/>
                            <ellipse cx="12" cy="19" rx="4" ry="2.5" fill="#8b4513"/>
                            <path d="M9 15C9 15 8 16 8 17C8 18 9 19 10 19M15 15C15 15 16 16 16 17C16 18 15 19 14 19" stroke="#654321" stroke-width="1" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="product-label">Quality Cassava</div>
                </div>
            </div>

            <p>We make sure that all trades are safe and easy for everyone involved.</p>

            <div class="cta-box">
                <center>
                    <a href="{{ url('/login') }}" class="cta-button">Go to Your Account</a>
                </center>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 20px;">Sign in with your email to start buying or selling.</p>
            </div>
        </div>

        <div class="footer">
            &copy; 2026 INTERNATIONAL AGRO MARKETPLACE (IAM).<br>
            Connecting Farmers and Buyers Globally.
        </div>
    </div>
</body>
</html>
