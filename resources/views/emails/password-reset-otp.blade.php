<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
        /* Client-specific Styles */
        #outlook a { padding: 0; } /* Force Outlook to provide a "view in browser" button. */
        body { width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; margin: 0; padding: 0; }
        .ExternalClass { width: 100%; } /* Force Hotmail to display emails at full width */
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        .MsoNormal { mso-style-priority: 99; mso-style-name: "Normal"; } /* Outlook fix for font sizes */
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; font-size: inherit !important; font-family: inherit !important; font-weight: inherit !important; line-height: inherit !important; }

        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f4f4;
        }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        td, th { padding: 0; }
        p { margin: 0 0 10px; }
        h1, h2, h3, h4, h5, h6 { margin: 0; padding: 0; line-height: 1.2; }
        img { border: none; -ms-interpolation-mode: bicubic; }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #16a34a; /* Green color for Our Restaurant brand */
            padding: 20px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 30px;
            background-color: #ffffff;
        }
        .otp-box {
            background-color: #fef2f2; /* Light red background for security */
            border: 1px solid #fecaca;
            border-radius: 5px;
            padding: 15px 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-box h2 {
            font-size: 36px; /* Larger for OTP */
            color: #333333;
            margin: 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #777777;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" valign="top">
                <table class="container" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="header">
                            <h1><i class="fas fa-lock"></i> Our Restaurant - Password Reset</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p style="font-size: 16px;">Hello {{ $name ?? 'User' }} <i class="fas fa-hand-wave"></i></p>

                            <p>We received a request to reset your password for your <strong>Our Restaurant</strong> account. To verify your identity and secure your account, please use the One-Time Password (OTP) below:</p>

                            <table class="otp-box" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <h2 style="font-size: 36px; color: #333333; margin: 0; font-weight: bold;">{{ $otp }}</h2>
                                    </td>
                                </tr>
                            </table>

                            <p><strong><i class="fas fa-clock"></i> Security Notice:</strong> This OTP is valid for a limited time and will expire <strong>{{ $expires }}</strong>.</p>

                            <p><strong><i class="fas fa-shield-alt"></i> Important Security Information:</strong></p>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li><i class="fas fa-exclamation-triangle"></i> Never share this code with anyone</li>
                                <li><i class="fas fa-envelope"></i> Our Restaurant will never ask for your password via email</li>
                                <li><i class="fas fa-ban"></i> If you didn't request this reset, please ignore this email</li>
                                <li><i class="fas fa-lock"></i> Consider enabling two-factor authentication for extra security</li>
                            </ul>

                            <p>If you have any concerns about your account security, please contact our support team immediately.</p>

                            <br>

                            <p>Stay secure! <i class="fas fa-lock"></i><br>
                            <strong style="color: #16a34a;">The Our Restaurant Security Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>&copy; {{ date('Y') }} Our Restaurant. All rights reserved.</p>
                            <p>
                                <a href="{{ url('/privacy-policy') }}" style="color: #16a34a; text-decoration: underline;">Privacy Policy</a> |
                                <a href="{{ url('/terms') }}" style="color: #16a34a; text-decoration: underline;">Terms of Service</a> |
                                <a href="{{ url('/contact') }}" style="color: #16a34a; text-decoration: underline;">Contact Support</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
