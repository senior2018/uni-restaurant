<!DOCTYPE html>
<html>
<head>
    <title>Support Ticket Response</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #333333; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .header { background-color: #16a34a; padding: 20px 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; }
        .content { padding: 30px; background-color: #ffffff; }
        .box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 5px; padding: 15px 20px; margin: 20px 0; }
        .box h2 { font-size: 20px; color: #333333; margin: 0 0 10px 0; font-weight: bold; }
        .subcopy { background-color: #f4f4f4; padding: 20px 30px; text-align: center; font-size: 12px; color: #777777; border-top: 1px solid #eeeeee; }
        .subcopy a { color: #16a34a; text-decoration: underline; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" valign="top">
                <table class="container" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="header">
                            <h1>🎧 Our Restaurant - Support Response</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p style="font-size: 16px;">Hello {{ $name ?? 'User' }} 👋</p>
                            <p>Thank you for contacting <strong>Our Restaurant</strong> support team! We've reviewed your inquiry and provided a response below. Your satisfaction is our priority, and we're here to help with any questions or concerns you may have.</p>
                            <div class="box">
                                <h2>📝 Your Original Message</h2>
                                <p style="margin-bottom: 0;">{{ $ticket->message }}</p>
                            </div>
                            <div class="box">
                                <h2>💬 Our Response</h2>
                                <p style="margin-bottom: 0;">{{ $admin_response }}</p>
                            </div>

                            <p><strong>Need more help?</strong> We're here for you! You can:</p>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>📧 Reply directly to this email</li>
                                <li>🌐 Visit our support center on our website</li>
                                <li>📞 Call us during business hours</li>
                                <li>💬 Chat with us live on our website</li>
                            </ul>

                            <p>We appreciate your feedback and look forward to serving you again soon!</p>
                            <br>
                            <p>Happy dining! 🍽️<br>
                            <strong style="color: #16a34a;">The Our Restaurant Support Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="subcopy">
                            @php
                                $supportEmail = 'support@' . parse_url(config('app.url'), PHP_URL_HOST);
                            @endphp
                            <p>Need urgent help? Contact our support team at <a href="mailto:{{ $supportEmail }}" style="color: #16a34a; text-decoration: underline;">{{ $supportEmail }}</a></p>
                            <p>&copy; {{ date('Y') }} Our Restaurant. All rights reserved. | <a href="{{ url('/privacy-policy') }}" style="color: #16a34a; text-decoration: underline;">Privacy Policy</a> | <a href="{{ url('/terms') }}" style="color: #16a34a; text-decoration: underline;">Terms of Service</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
