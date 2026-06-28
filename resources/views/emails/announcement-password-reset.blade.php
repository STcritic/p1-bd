<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restauro de acesso</title>
</head>
<body style="margin:0;padding:0;background:#eef5fb;font-family:Arial,Helvetica,sans-serif;color:#10243a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef5fb;padding:32px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #dbe7f0;box-shadow:0 18px 50px rgba(6,47,95,.12);">
                    <tr>
                        <td style="padding:34px 34px 18px;border-top:5px solid #1266c3;">
                            <div style="font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#1266c3;font-weight:700;">Business Diversity</div>
                            <h1 style="margin:18px 0 10px;font-size:30px;line-height:1.1;color:#10243a;">Restauro de acesso</h1>
                            <p style="margin:0;color:#5c7084;font-size:16px;line-height:1.65;">Olá {{ $admin->name }}, use o botão abaixo para definir uma nova palavra-passe no Portal BD.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 34px;">
                            <a href="{{ $resetUrl }}" style="display:inline-block;background:#1266c3;color:#ffffff;text-decoration:none;font-weight:700;padding:15px 22px;">Definir nova palavra-passe →</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 34px 34px;">
                            <p style="margin:0 0 14px;color:#5c7084;font-size:14px;line-height:1.6;">Este link é válido por {{ $expiresMinutes }} minutos. Se não solicitou este restauro, pode ignorar este email.</p>
                            <p style="margin:0;color:#7a8ea0;font-size:12px;line-height:1.6;">Se o botão não abrir, copie e cole este link no navegador:<br><span style="word-break:break-all;color:#1266c3;">{{ $resetUrl }}</span></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
