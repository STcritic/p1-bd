<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nova marcação</title>
</head>
<body style="margin:0;padding:0;background:#eef5fb;font-family:Arial,Helvetica,sans-serif;color:#10243a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef5fb;padding:32px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #dbe7f0;">
                <tr><td style="padding:34px;border-top:5px solid #1266c3;">
                    <div style="font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#1266c3;font-weight:700;">Business Diversity</div>
                    <h1 style="margin:18px 0 10px;font-size:28px;line-height:1.1;color:#10243a;">Nova marcação no website</h1>
                    <p style="margin:0;color:#5c7084;font-size:15px;line-height:1.65;">Uma nova reunião foi marcada pelo website.</p>
                </td></tr>
                <tr><td style="padding:0 34px 30px;">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fb;border:1px solid #dce8f0;">
                        <tr><td style="padding:18px;line-height:1.7;">
                            <strong>Nome:</strong> {{ $appointment->name }}<br>
                            <strong>Email:</strong> {{ $appointment->email }}<br>
                            @if ($appointment->phone)<strong>Telefone:</strong> {{ $appointment->phone }}<br>@endif
                            @if ($appointment->organization)<strong>Organização:</strong> {{ $appointment->organization }}<br>@endif
                            @if ($appointment->position)<strong>Cargo:</strong> {{ $appointment->position }}<br>@endif
                            <strong>Data:</strong> {{ $appointment->scheduledLocal()->format('d/m/Y H:i') }}<br>
                            <strong>Assunto:</strong> {{ $appointment->subject }}<br>
                            @if ($appointment->message)<strong>Contexto:</strong><br>{{ $appointment->message }}<br>@endif
                            @if ($appointment->meeting_url)<strong>Link:</strong> <a href="{{ $appointment->meeting_url }}" style="color:#1266c3;">{{ $appointment->meeting_url }}</a><br>@endif
                        </td></tr>
                    </table>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
