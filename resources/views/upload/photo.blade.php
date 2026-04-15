<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Fotos hochladen – {{ $device->ticket_number }}</title>
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

        .device-name { font-size: 18px; font-weight: 700; color: #f1f5f9; }
        .device-sub  { font-size: 13px; color: #64748b; margin-top: 2px; }

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
        .dropzone-icon  { font-size: 44px; margin-bottom: 12px; line-height: 1; }
        .dropzone-title { font-size: 15px; font-weight: 600; color: #e2e8f0; margin-bottom: 6px; }
        .dropzone-sub   { font-size: 12px; color: #64748b; line-height: 1.5; }

        /* Thumbnail grid */
        #preview-grid {
            display: none;
            margin-top: 16px;
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }
        .thumb-wrap {
            position: relative; border-radius: 8px; overflow: hidden;
            aspect-ratio: 1; background: #0f172a;
        }
        .thumb-wrap img {
            width: 100%; height: 100%; object-fit: cover;
            display: block;
        }
        .thumb-remove {
            position: absolute; top: 4px; right: 4px;
            width: 20px; height: 20px; border-radius: 50%;
            background: rgba(0,0,0,.6); border: none; color: #fff;
            font-size: 11px; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            line-height: 1;
        }

        .preview-count {
            font-size: 12px; color: #64748b; margin-top: 8px; text-align: center;
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
                    <input type="file" name="photos[]" id="photo-input"
                           accept="image/*" multiple>
                    <div class="dropzone-icon">📷</div>
                    <div class="dropzone-title">Fotos auswählen</div>
                    <div class="dropzone-sub">Mehrere Fotos aus der Galerie wählen<br>oder neue Fotos aufnehmen</div>
                </div>

                <div id="preview-grid"></div>
                <div id="preview-count" class="preview-count" style="display:none;"></div>

                <div id="progress-wrap">
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" id="progress-fill"></div>
                    </div>
                    <div class="progress-label" id="progress-label">Wird hochgeladen…</div>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn" disabled>
                    Fotos hochladen
                </button>
            </form>

            <div class="note">
                ⚠️ Dieser Link ist einmalig verwendbar und verfällt nach dem Hochladen.
                Ein neuer Link wird automatisch generiert.
            </div>
        </div>
    </div>

    <script>
        const input        = document.getElementById('photo-input');
        const grid         = document.getElementById('preview-grid');
        const countEl      = document.getElementById('preview-count');
        const submitBtn    = document.getElementById('submit-btn');
        const dropzone     = document.getElementById('dropzone');
        const form         = document.getElementById('upload-form');
        const progressWrap = document.getElementById('progress-wrap');
        const progressFill = document.getElementById('progress-fill');
        const progressLabel = document.getElementById('progress-label');

        // Keep a DataTransfer so we can remove individual files
        let dt = new DataTransfer();

        input.addEventListener('change', () => {
            Array.from(input.files).forEach(file => dt.items.add(file));
            input.files = dt.files;
            renderPreviews();
        });

        function renderPreviews() {
            grid.innerHTML = '';
            const files = Array.from(dt.files);

            if (files.length === 0) {
                grid.style.display = 'none';
                countEl.style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Fotos hochladen';
                return;
            }

            grid.style.display = 'grid';
            countEl.style.display = 'block';
            countEl.textContent = files.length + (files.length === 1 ? ' Foto ausgewählt' : ' Fotos ausgewählt');
            submitBtn.disabled = false;
            submitBtn.textContent = files.length === 1 ? 'Foto hochladen' : files.length + ' Fotos hochladen';

            files.forEach((file, i) => {
                const wrap = document.createElement('div');
                wrap.className = 'thumb-wrap';

                const img = document.createElement('img');
                const reader = new FileReader();
                reader.onload = e => img.src = e.target.result;
                reader.readAsDataURL(file);

                const btn = document.createElement('button');
                btn.className = 'thumb-remove';
                btn.type = 'button';
                btn.textContent = '✕';
                btn.onclick = () => removeFile(i);

                wrap.appendChild(img);
                wrap.appendChild(btn);
                grid.appendChild(wrap);
            });
        }

        function removeFile(index) {
            const newDt = new DataTransfer();
            Array.from(dt.files).forEach((f, i) => { if (i !== index) newDt.items.add(f); });
            dt = newDt;
            input.files = dt.files;
            renderPreviews();
        }

        // Drag-over highlight
        dropzone.addEventListener('dragover',  e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
        dropzone.addEventListener('drop',      e => { e.preventDefault(); dropzone.classList.remove('drag-over'); });

        // Show progress on submit
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            progressWrap.style.display = 'block';
            let pct = 0;
            const iv = setInterval(() => {
                pct = Math.min(pct + Math.random() * 12, 90);
                progressFill.style.width = pct + '%';
                progressLabel.textContent = 'Wird hochgeladen… ' + Math.round(pct) + '%';
                if (pct >= 90) clearInterval(iv);
            }, 200);
        });
    </script>
</body>
</html>
