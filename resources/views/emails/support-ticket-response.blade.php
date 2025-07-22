<!DOCTYPE html>
<html>
<head>
    <title>Support Ticket Response</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
        body { font-family: Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #333333; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .header { background-color: #007bff; padding: 20px 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 24px; margin: 0; }
        .content { padding: 30px; background-color: #ffffff; }
        .box { background-color: #f0f8ff; border: 1px solid #cceeff; border-radius: 5px; padding: 15px 20px; margin: 20px 0; }
        .box h2 { font-size: 20px; color: #333333; margin: 0 0 10px 0; font-weight: bold; }
        .subcopy { background-color: #f4f4f4; padding: 20px 30px; text-align: center; font-size: 12px; color: #777777; border-top: 1px solid #eeeeee; }
        .subcopy a { color: #007bff; text-decoration: underline; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" valign="top">
                <table class="container" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="header">
                            <h1>Support Ticket Response</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p style="font-size: 16px;">Hello {{ $name ?? 'User' }} 👋</p>
                            <p>We have responded to your support request. Please see the details below:</p>
                            <div class="box">
                                <h2>Your Message</h2>
                                <p style="margin-bottom: 0;">{{ $ticket->message }}</p>
                            </div>
                            <div class="box">
                                <h2>Admin Response</h2>
                                <p style="margin-bottom: 0;">{{ $admin_response }}</p>
                            </div>
                            <p>If you have further questions, feel free to reply to this email or contact us again.</p>
                            <br>
                            <p>Thanks,<br>
                            <strong style="color: #333333;">The {{ config('app.name') }} Support Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="subcopy">
                            @php
                                $supportEmail = 'support@' . parse_url(config('app.url'), PHP_URL_HOST);
                            @endphp
                            <p>Need urgent help? Contact support at <a href="mailto:{{ $supportEmail }}" style="color: #007bff; text-decoration: underline;">{{ $supportEmail }}</a></p>
                            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
