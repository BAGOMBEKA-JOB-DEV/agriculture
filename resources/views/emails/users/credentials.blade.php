<!DOCTYPE html>
<html>
<head>
    <title>Your Account Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #2e7d32; margin-top: 0;">Welcome to the Identity Registry</h2>
        <p>Hello {{ $user->name }},</p>
        <p>Your account has been successfully provisioned in the Governance Nexus.</p>
        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #2e7d32; margin: 20px 0;">
            <p style="margin: 0; font-size: 14px; color: #555;">Login Email:</p>
            <p style="margin: 5px 0 10px; font-size: 16px; font-weight: bold; color: #333;">{{ $user->email }}</p>
            
            <p style="margin: 10px 0 0; font-size: 14px; color: #555;">Temporary Password:</p>
            <p style="margin: 5px 0 0; font-size: 18px; font-weight: bold; color: #333;">{{ $password }}</p>
        </div>

        <p>Please login immediately and change your password.</p>
        
        <a href="{{ url('/login') }}" style="display: inline-block; background-color: #2e7d32; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold; margin-top: 10px;">Access Portal</a>
    </div>
</body>
</html>
