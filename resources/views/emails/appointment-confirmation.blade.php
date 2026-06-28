<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmação de reunião</title>
</head>
<body style="margin:0;padding:0;background:#eef5fb;font-family:Arial,Helvetica,sans-serif;color:#10243a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef5fb;padding:32px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #dbe7f0;box-shadow:0 18px 50px rgba(6,47,95,.12);">
                <tr><td style="padding:34px 34px 18px;border-top:5px solid #1266c3;">
                    <div style="font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#1266c3;font-weight:700;">Business Diversity</div>
                    <h1 style="margin:18px 0 10px;font-size:30px;line-height:1.1;color:#10243a;">Reunião confirmada</h1>
                    <p style="margin:0;color:#5c7084;font-size:16px;line-height:1.65;">Olá {{ $appointment->name }}, a sua reunião foi marcada.</p>
                </td></tr>
                <tr><td style="padding:18px 34px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fb;border:1px solid #dce8f0;">
                        <tr><td style="padding:18px;">
                            <strong>Data:</strong> {{ $appointment->scheduledLocal()->format('d/m/Y H:i') }}<br>
                            <strong>Duração:</strong> {{ $appointment->duration_minutes }} minutos<br>
                            <strong>Assunto:</strong> {{ $appointment->subject }}<br>
                            @if ($appointment->meeting_platform)<strong>Plataforma:</strong> {{ $appointment->meeting_platform }}<br>@endif
                            @if ($appointment->meeting_url)<strong>Link:</strong> <a href="{{ $appointment->meeting_url }}" style="color:#1266c3;">{{ $appointment->meeting_url }}</a><br>@endif
                            @if ($appointment->meeting_id)<strong>ID:</strong> {{ $appointment->meeting_id }}<br>@endif
                            @if ($appointment->meeting_password)<strong>Senha:</strong> {{ $appointment->meeting_password }}<br>@endif
                        </td></tr>
                    </table>
                </td></tr>
                @if ($setting->standard_message || $appointment->location_notes)
                    <tr><td style="padding:0 34px 18px;color:#5c7084;font-size:14px;line-height:1.65;">
                        @if ($setting->standard_message)<p style="margin:0 0 12px;">{{ $setting->standard_message }}</p>@endif
                        @if ($appointment->location_notes)<p style="margin:0;">{{ $appointment->location_notes }}</p>@endif
                    </td></tr>
                @endif
                <tr><td style="padding:8px 34px 34px;">
                    <a href="{{ $appointment->googleCalendarUrl() }}" style="display:inline-block;background:#1266c3;color:#ffffff;text-decoration:none;font-weight:700;padding:15px 22px;">Adicionar ao Google Calendar →</a>
                    @if ($appointment->meeting_url)
                        <a href="{{ $appointment->meeting_url }}" style="display:inline-block;margin-left:10px;background:#10243a;color:#ffffff;text-decoration:none;font-weight:700;padding:15px 22px;">Entrar na reunião ↗</a>
                    @endif
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
