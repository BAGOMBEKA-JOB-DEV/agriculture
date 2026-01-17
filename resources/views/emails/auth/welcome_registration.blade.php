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
                    <img src="{{ $message->embed('/Users/job/.gemini/antigravity/brain/d3cfe9bd-58f9-4e8d-8be2-1b4dc532dd63/maize_harvest_detailed_1767884352769.png') }}" class="product-img">
                    <div class="product-label">Golden Maize</div>
                </div>
                <div class="product-card">
                    <img src="{{ $message->embed('/Users/job/.gemini/antigravity/brain/d3cfe9bd-58f9-4e8d-8be2-1b4dc532dd63/coffee_beans_premium_1767884368666.png') }}" class="product-img">
                    <div class="product-label">Premium Coffee</div>
                </div>
                <div class="product-card">
                    <img src="{{ $message->embed('/Users/job/.gemini/antigravity/brain/d3cfe9bd-58f9-4e8d-8be2-1b4dc532dd63/cassava_tubers_commercial_1767884389422.png') }}" class="product-img">
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
