<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Foto hochgeladen – {{ $device->ticket_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a; color: #f1f5f9;
            min-height: 100dvh;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 24px 16px;
        }
        .icon { font-size: 72px; margin-bottom: 20px; animation: pop .4s cubic-bezier(.175,.885,.32,1.275); }
        @keyframes pop { from { transform: scale(0); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        h1 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        p  { font-size: 14px; color: #64748b; text-align: center; line-height: 1.6; }
        .ticket {
            margin-top: 24px; padding: 10px 20px; border-radius: 10px;
            background: rgba(99,102,241,.1); border: 1px solid rgba(99,102,241,.25);
            color: #818cf8; font-size: 13px; font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="icon">✅</div>
    <h1>Foto gesendet!</h1>
    <p>{{ $count ?? 1 }} {{ ($count ?? 1) === 1 ? 'Foto wurde' : 'Fotos wurden' }} erfolgreich für<br>Ticket <strong>{{ $device->ticket_number }}</strong> hochgeladen.</p>
    <div class="ticket">{{ $device->brand }} {{ $device->model }}</div>
    <p style="margin-top:24px; font-size:12px;">Du kannst dieses Fenster jetzt schließen.</p>
</body>
</html>
