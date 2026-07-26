<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f7; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:12px; overflow:hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 36px 40px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700; letter-spacing:-0.5px;">
                                {{ config('app.name') }}
                            </h1>
                            <p style="margin:8px 0 0; color:rgba(255,255,255,0.8); font-size:14px;">Secure Login Verification</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px 40px 32px;">
                            <p style="margin:0 0 20px; font-size:16px; color:#374151; line-height:1.6;">
                                Hi there 👋
                            </p>
                            <p style="margin:0 0 28px; font-size:15px; color:#6b7280; line-height:1.7;">
                                Use the one-time password below to log in to your account. This OTP is valid for <strong style="color:#374151;">10 minutes</strong>.
                            </p>

                            {{-- OTP Box --}}
                            <div style="background: #f0f0ff; border: 2px dashed #a5b4fc; border-radius: 12px; padding: 28px; text-align:center; margin-bottom:28px;">
                                <p style="margin:0 0 8px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:#6366f1;">Your OTP</p>
                                <p style="margin:0; font-size:42px; font-weight:800; letter-spacing:10px; color:#4338ca; font-variant-numeric: tabular-nums;">{{ $otp }}</p>
                            </div>

                            <p style="margin:0 0 8px; font-size:13px; color:#9ca3af; line-height:1.6;">
                                ⚠️ Do not share this OTP with anyone. Our team will never ask for it.
                            </p>
                            <p style="margin:0; font-size:13px; color:#9ca3af; line-height:1.6;">
                                If you did not request this, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9fafb; padding: 20px 40px; border-top:1px solid #e5e7eb; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#9ca3af;">
                                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
