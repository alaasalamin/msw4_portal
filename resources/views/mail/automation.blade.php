<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#0f172a;border-radius:16px 16px 0 0;padding:28px 36px;text-align:center;">
                            <div style="font-size:13px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#6366f1;">
                                MSW Repair
                            </div>
                            @if ($ticketNumber)
                                <div style="margin-top:6px;font-size:11px;color:#475569;letter-spacing:.05em;">
                                    Ticket {{ $ticketNumber }}
                                    @if ($deviceLabel)— {{ $deviceLabel }}@endif
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background:#ffffff;padding:36px 36px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <div style="font-size:15px;line-height:1.75;color:#1e293b;white-space:pre-line;">{{ $body }}</div>
                        </td>
                    </tr>

                    {{-- Divider --}}
                    <tr>
                        <td style="background:#ffffff;padding:0 36px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                            <div style="height:1px;background:#f1f5f9;"></div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#ffffff;border-radius:0 0 16px 16px;padding:20px 36px 28px;
                                   border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
                            <div style="font-size:12px;color:#94a3b8;line-height:1.6;">
                                Diese E-Mail wurde automatisch von MSW Repair gesendet.<br>
                                Bitte antworten Sie nicht direkt auf diese E-Mail.
                            </div>
                        </td>
                    </tr>

                    {{-- Bottom spacer --}}
                    <tr>
                        <td style="padding:20px 0;text-align:center;">
                            <div style="font-size:11px;color:#94a3b8;">
                                © {{ date('Y') }} MSW Repair
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
