<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body>
    <p>Hello {{ $name ?? 'User' }},</p>

    <p>You requested to reset your password. Use the OTP below to verify your identity:</p>

    <h2 style="text-align: center; font-size: 32px;">{{ $otp }}</h2>

    <p>This OTP will expire {{ $expires }}.</p>

    <p>If you did not request this, please ignore this email.</p>

    <br>

    <p>Regards,<br>Your App Team</p>
</body>
</html>
