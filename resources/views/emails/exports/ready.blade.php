<!DOCTYPE html>
<html>
<head>
    <title>Data Export Ready</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #2e7d32; margin-top: 0;">Export Ready 📄</h2>
        <p>Dear {{ $user->name }},</p>
        <p>Your requested data export (**{{ $format }}**) has been successfully generated and is prepared for download.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #2e7d32; margin: 20px 0;">
            <p style="margin: 0; font-size: 14px; color: #555;">Export Details:</p>
            <p style="margin: 5px 0 0; font-size: 16px; font-weight: bold; color: #333;">Type: {{ $format }}</p>
            <p style="margin: 5px 0 0; font-size: 14px; color: #555;">Generated: {{ now()->format('M d, Y H:i') }}</p>
        </div>

        <p>This file contains sensitive governance data. Please ensure it is stored securely.</p>
        
        <a href="{{ $url }}" style="display: inline-block; background-color: #2e7d32; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold; margin-top: 10px;">Download Export File</a>

        <p style="margin-top: 30px; font-size: 12px; color: #999;">If you did not request this export, please contact the security team immediately.</p>
    </div>
</body>
</html>
