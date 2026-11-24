<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая запись #{{ $booking->id }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#18181b;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding: 24px 12px;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                <tr>
                    <td style="background: linear-gradient(135deg, #f43f5e, #ec4899); padding: 24px; color:#ffffff;">
                        <h1 style="margin:0; font-size:20px; font-weight:700;">Новая заявка на запись
                            #{{ $booking->id }}</h1>
                        <p style="margin:6px 0 0; font-size:14px; opacity:0.9;">
                            Поступила {{ $booking->created_at?->format('d.m.Y в H:i') }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 24px;">
                        <table width="100%" cellpadding="0" cellspacing="0"
                               style="border-collapse: collapse; font-size:15px;">
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a; width:38%;">
                                    Услуга:
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; font-weight:600;">{{ $booking->service?->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a;">Дата и
                                    время:
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; font-weight:600; color:#e11d48;">
                                    {{ $booking->appointment_time?->format('d.m.Y в H:i') ?? '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a;">Клиент:</td>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; font-weight:600;">{{ $booking->client_name }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a;">Телефон:
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7;">
                                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $booking->client_phone) }}"
                                       style="color:#18181b; text-decoration:none; font-weight:600;">
                                        {{ $booking->client_phone }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a;">Email:</td>
                                <td style="padding:10px 0; border-bottom:1px solid #e4e4e7;">
                                    <a href="mailto:{{ $booking->client_email }}"
                                       style="color:#2563eb; text-decoration:none;">
                                        {{ $booking->client_email }}
                                    </a>
                                </td>
                            </tr>
                            @if($booking->message)
                                <tr>
                                    <td style="padding:10px 0; border-bottom:1px solid #e4e4e7; color:#71717a; vertical-align:top;">
                                        Комментарий:
                                    </td>
                                    <td style="padding:10px 0; border-bottom:1px solid #e4e4e7;">{{ $booking->message }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td style="padding:10px 0; color:#71717a;">Источник:</td>
                                <td style="padding:10px 0; font-size:13px; color:#52525b;">
                                    {{ $booking->source ?? 'website' }}
                                    @if($booking->utm_source || $booking->utm_campaign)
                                        ({{ implode(' / ', array_filter([$booking->utm_source, $booking->utm_medium, $booking->utm_campaign])) }}
                                        )
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 28px; text-align: center;">
                            <a href="{{ $adminUrl }}"
                               style="display:inline-block; background:#e11d48; color:#ffffff; text-decoration:none; font-weight:600; font-size:15px; padding:12px 28px; border-radius:8px;">
                                Открыть запись в админке
                            </a>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
