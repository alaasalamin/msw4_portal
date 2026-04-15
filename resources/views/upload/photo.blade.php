<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Foto hochladen – {{ $device->ticket_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            min-height: 100dvh;
            display: flex; flex-direction: column; align-items: center;
            padding: 24px 16px 40px;
        }

        .logo {
            font-size: 13px; font-weight: 700; letter-spacing: .08em;
            color: #6366f1; text-transform: uppercase; margin-bottom: 32px;
        }

        .card {
            width: 100%; max-width: 420px;
            background: #1e293b;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 20px;
            overflow: hidden;
        }

        .card-header {
            background: #0f172a;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }

        .ticket {
            font-size: 11px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #6366f1; margin-bottom: 4px;
        }

        .device-name {
            font-size: 18px; font-weight: 700; color: #f1f5f9;
        }

        .device-sub {
            font-size: 13px; color: #64748b; margin-top: 2px;
        }

        .card-body { padding: 24px; }

        /* Drop zone */
        .dropzone {
            border: 2px dashed rgba(99,102,241,.4);
            border-radius: 14px;
            padding: 36px 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
            background: rgba(99,102,241,.04);
        }
        .dropzone:hover, .dropzone.drag-over {
            border-color: #6366f1;
            background: rgba(99,102,241,.08);
        }

        .dropzone input[type="file"] {
            position: absolute; inset: 0; opacity: 0;
            cursor: pointer; width: 100%; height: 100%;
        }

        .dropzone-icon { font-size: 44px; margin-bottom: 12px; line-height: 1; }

        .dropzone-title {
            font-size: 15px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px;
        }

        .dropzone-sub { font-size: 12px; color: #64748b; line-height: 1.5; }

        /* Preview */
        #preview-wrap { display: none; margin-top: 16px; }
        #preview-img {
            width: 100%; max-height: 260px; object-fit: cover;
            border-radius: 10px; border: 1px solid rgba(255,255,255,.1);
        }
        .preview-name {
            font-size: 12px; color: #64748b; margin-top: 6px; text-align: center;
        }

        /* Submit */
        .btn-submit {
            display: block; width: 100%; margin-top: 20px;
            padding: 14px; border-radius: 12px; border: none;
            font-size: 15px; font-weight: 700; cursor: pointer;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #fff;
            transition: opacity .15s, transform .1s;
        }
        .btn-submit:hover   { opacity: .9; }
        .btn-submit:active  { transform: scale(.98); }
        .btn-submit:disabled { opacity: .4; cursor: not-allowed; }

        /* Error */
        .error-box {
            margin-top: 14px; padding: 10px 14px; border-radius: 8px;
            background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3);
            color: #f87171; font-size: 13px;
        }

        /* Progress */
        #progress-wrap { display: none; margin-top: 16px; }
        .progress-bar-bg {
            height: 6px; border-radius: 3px; background: rgba(255,255,255,.1); overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%; border-radius: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            width: 0%; transition: width .2s;
        }
        .progress-label { font-size: 12px; color: #64748b; margin-top: 6px; text-align: center; }

        .note {
            margin-top: 24px; padding: 12px 16px; border-radius: 10px;
            background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.2);
            font-size: 12px; color: #fbbf24; line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="logo">MSW Repair</div>

    <div class="card">
        <div class="card-header">
            <div class="ticket">{{ $device->ticket_number }}</div>
            <div class="device-name">{{ $device->brand }} {{ $device->model }}</div>
            @if($device->issue_description)
                <div class="device-sub">{{ Str::limit($device->issue_description, 60) }}</div>
            @endif
        </div>

        <div class="card-body">
            <form method="POST" action="{{ url('/upload/' . $token) }}" enctype="multipart/form-data" id="upload-form">
                @csrf

                @if($errors->any())
                    <div class="error-box">{{ $errors->first() }}</div>
                @endif

                <div class="dropzone" id="dropzone">
                    <input type="file" name="photo" id="photo-input"
                           accept="image/*" capture="environment">
                    <div class="dropzone-icon">📷</div>
                    <div class="dropzone-title">Foto aufnehmen oder wählen</div>
                    <div class="dropzone-sub">
                        Tippe hier um die Kamera zu öffnen<br>
                        oder ein Bild aus der Galerie zu wählen
                    </div>
                </div>

                <div id="preview-wrap">
                    <img id="preview-img" src="" alt="Vorschau">
                    <div class="preview-label" id="preview-name"></div>
                </div>

                <div id="progress-wrap">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-label" id="progress-label">Wird hochgeladen…</div>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn" disabled>
                    Foto hochladen
                </button>
            </form>

            <div class="note">
                ⚠️ Dieser Link ist einmalig verwendbar und verfällt nach dem Hochladen.
                Ein neuer Link wird automatisch generiert.
            </div>
        </div>
    </div>

    <script>
        const input    = document.getElementById('photo-input');
        const preview  = document.getElementById('preview-img');
        const previewW = document.getElementById('preview-wrap');
        const nameEl   = document.getElementById('preview-name');
        const submitBtn = document.getElementById('submit-btn');
        const dropzone = document.getElementById('dropzone');
        const form     = document.getElementById('upload-form');
        const progressWrap = document.getElementById('progress-wrap');
        const progressFill = document.getElementById('progress-fill');
        const progressLabel = document.getElementById('progress-label');

        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                previewW.style.display = 'block';
                nameEl.textContent = file.name;
                submitBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        });

        // Drag-over highlight
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
        dropzone.addEventListener('drop', e => { e.preventDefault(); dropzone.classList.remove('drag-over'); });

        // Show progress on submit
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            progressWrap.style.display = 'block';
            let pct = 0;
            const iv = setInterval(() => {
                pct = Math.min(pct + Math.random() * 15, 90);
                progressFill.style.width = pct + '%';
                progressLabel.textContent = 'Wird hochgeladen… ' + Math.round(pct) + '%';
                if (pct >= 90) clearInterval(iv);
            }, 200);
        });
    </script>
</body>
</html>
