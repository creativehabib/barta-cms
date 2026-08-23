<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Hind Siliguri','Noto Sans Bengali',Arial,sans-serif; color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                    {{-- Masthead --}}
                    <tr>
                        <td style="background-color:#c81420; padding:20px 32px;">
                            <span style="color:#ffffff; font-size:22px; font-weight:800; letter-spacing:-0.5px;">{{ $siteName }}</span>
                        </td>
                    </tr>

                    {{-- Subject line --}}
                    <tr>
                        <td style="padding:32px 32px 0 32px;">
                            <h1 style="margin:0; font-size:24px; line-height:1.3; font-weight:800; color:#111827;">{{ $subject }}</h1>
                        </td>
                    </tr>

                    {{-- Body (HTML content authored in the admin editor) --}}
                    <tr>
                        <td style="padding:16px 32px 32px 32px; font-size:16px; line-height:1.7; color:#374151;">
                            {!! $body !!}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 32px; background-color:#f9fafb; border-top:1px solid #e5e7eb; font-size:13px; line-height:1.6; color:#6b7280;">
                            <p style="margin:0 0 8px 0;">
                                &copy; {{ date('Y') }} {{ $siteName }}.
                                {{ $locale === 'bn' ? 'সর্বস্বত্ব সংরক্ষিত।' : 'All rights reserved.' }}
                            </p>
                            <p style="margin:0;">
                                {{ $locale === 'bn' ? 'এই নিউজলেটার আর পেতে না চাইলে' : 'No longer want these emails?' }}
                                <a href="{{ $unsubscribeUrl }}" style="color:#c81420; text-decoration:underline;">{{ $locale === 'bn' ? 'সাবস্ক্রিপশন বাতিল করুন' : 'Unsubscribe' }}</a>.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
