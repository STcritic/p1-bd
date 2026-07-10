@php
    $details = $payload['details'] ?? [];
    $notes = array_filter((array) ($payload['notes'] ?? []));
    $action = $payload['action'] ?? null;
    $secondaryAction = $payload['secondary_action'] ?? null;
    $footer = $payload['footer'] ?? 'Business Diversity';
@endphp
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $payload['subject'] ?? 'Business Diversity' }}</title>
</head>
<body style="margin:0;padding:0;background:#edf4fa;font-family:Arial,Helvetica,sans-serif;color:#10243a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#edf4fa;padding:34px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:660px;background:#ffffff;border:1px solid #d8e5ee;box-shadow:0 20px 55px rgba(5,45,88,.13);">
                    <tr>
                        <td style="padding:0;background:linear-gradient(135deg,#062f5f 0%,#1266c3 100%);height:8px;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:34px 36px 18px;">
                            <div style="font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#1266c3;font-weight:700;">
                                {{ $payload['eyebrow'] ?? 'Business Diversity' }}
                            </div>
                            <h1 style="margin:18px 0 12px;font-size:30px;line-height:1.12;color:#10243a;font-weight:800;">
                                {{ $payload['title'] ?? 'Notificação BD' }}
                            </h1>
                            @if(!empty($payload['intro']))
                                <p style="margin:0;color:#5c7084;font-size:16px;line-height:1.65;">
                                    {{ $payload['intro'] }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    @if($details)
                        <tr>
                            <td style="padding:12px 36px 20px;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f9fc;border:1px solid #dce8f0;">
                                    @foreach($details as $label => $value)
                                        @continue($value === null || $value === '')
                                        <tr>
                                            <td style="padding:13px 16px;border-bottom:1px solid #e2edf4;width:34%;vertical-align:top;color:#5c7084;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;">
                                                {{ is_string($label) ? $label : 'Detalhe' }}
                                            </td>
                                            <td style="padding:13px 16px;border-bottom:1px solid #e2edf4;vertical-align:top;color:#10243a;font-size:15px;line-height:1.55;">
                                                @if(is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                                                    <a href="{{ $value }}" style="color:#1266c3;text-decoration:none;font-weight:700;word-break:break-all;">{{ $value }}</a>
                                                @else
                                                    {!! nl2br(e(is_array($value) ? implode(', ', $value) : (string) $value)) !!}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif

                    @if($notes)
                        <tr>
                            <td style="padding:0 36px 22px;color:#5c7084;font-size:14px;line-height:1.65;">
                                @foreach($notes as $note)
                                    <p style="margin:0 0 8px;">{{ $note }}</p>
                                @endforeach
                            </td>
                        </tr>
                    @endif

                    @if(is_array($action) && !empty($action['url']) && !empty($action['label']))
                        <tr>
                            <td style="padding:4px 36px 34px;">
                                <a href="{{ $action['url'] }}" style="display:inline-block;background:#1266c3;color:#ffffff;text-decoration:none;font-weight:800;padding:15px 22px;">
                                    {{ $action['label'] }} →
                                </a>
                                @if(is_array($secondaryAction) && !empty($secondaryAction['url']) && !empty($secondaryAction['label']))
                                    <a href="{{ $secondaryAction['url'] }}" style="display:inline-block;margin-left:10px;background:#082f5f;color:#ffffff;text-decoration:none;font-weight:800;padding:15px 22px;">
                                        {{ $secondaryAction['label'] }} ↗
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:20px 36px 30px;border-top:1px solid #e3edf4;color:#7a8ea0;font-size:12px;line-height:1.6;">
                            {{ $footer }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
