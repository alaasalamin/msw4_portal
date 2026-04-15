<x-filament-widgets::widget>
    <style>
        /* ── Notes widget ─────────────────────────────────────────────── */
        .dn-wrap {
            border-radius: 16px;
            overflow: hidden;
        }
        .dn-wrap { background: #fff; border: 1px solid rgba(0,0,0,.07); }
        .dark .dn-wrap { background: #111827; border-color: rgba(255,255,255,.07); }

        /* Header */
        .dn-header {
            display: flex; align-items: center; gap: 10px;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0,0,0,.06);
        }
        .dn-header { background: #f9fafb; }
        .dark .dn-header { background: #0f172a; border-color: rgba(255,255,255,.06); }

        .dn-header-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            background: rgba(99,102,241,.12);
            border: 1px solid rgba(99,102,241,.25);
        }
        .dn-header-title { font-size: 14px; font-weight: 700; flex: 1; }
        .dn-header-title { color: #111827; }
        .dark .dn-header-title { color: #f3f4f6; }

        .dn-count-badge {
            font-size: 11px; font-weight: 600; padding: 2px 8px;
            border-radius: 20px;
            background: rgba(99,102,241,.1); color: #6366f1;
            border: 1px solid rgba(99,102,241,.25);
        }

        /* Photo upload button */
        .dn-photo-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px; border-radius: 8px; border: none; cursor: pointer;
            font-size: 12px; font-weight: 600;
            background: rgba(16,185,129,.1); color: #10b981;
            border: 1px solid rgba(16,185,129,.25);
            transition: background .15s;
        }
        .dn-photo-btn:hover { background: rgba(16,185,129,.18); }

        /* ── Compose area ─────────────────────────────────────────────── */
        .dn-compose {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0,0,0,.06);
        }
        .dark .dn-compose { border-color: rgba(255,255,255,.06); }

        .dn-compose-textarea {
            width: 100%; border-radius: 10px;
            padding: 10px 14px; font-size: 13px; line-height: 1.6;
            resize: vertical; min-height: 80px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .dn-compose-textarea {
            background: #f9fafb; border: 1px solid rgba(0,0,0,.12); color: #111827;
        }
        .dark .dn-compose-textarea {
            background: #1e293b; border-color: rgba(255,255,255,.1); color: #f3f4f6;
        }
        .dn-compose-textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .dn-compose-textarea::placeholder { color: #9ca3af; }

        .dn-compose-footer {
            display: flex; align-items: center; gap: 10px;
            margin-top: 10px; flex-wrap: wrap;
        }

        /* Toggle */
        .dn-toggle-wrap { display: flex; align-items: center; gap: 6px; cursor: pointer; }
        .dn-toggle-label { font-size: 12px; font-weight: 500; }
        .dn-toggle-label { color: #6b7280; }
        .dark .dn-toggle-label { color: #9ca3af; }

        .dn-toggle {
            width: 36px; height: 20px; border-radius: 10px;
            border: none; cursor: pointer; padding: 0; position: relative;
            transition: background .2s;
            flex-shrink: 0;
        }
        .dn-toggle.off { background: rgba(107,114,128,.3); }
        .dn-toggle.on  { background: #6366f1; }
        .dn-toggle::after {
            content: ''; position: absolute; top: 2px; left: 2px;
            width: 16px; height: 16px; border-radius: 50%; background: #fff;
            transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.3);
        }
        .dn-toggle.on::after { transform: translateX(16px); }

        /* Submit button */
        .dn-submit {
            margin-left: auto;
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 8px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            background: #6366f1; color: #fff;
            transition: background .15s, transform .1s;
        }
        .dn-submit:hover { background: #4f46e5; }
        .dn-submit:active { transform: scale(.97); }
        .dn-submit:disabled { opacity: .5; cursor: not-allowed; }

        /* Flash message */
        .dn-flash {
            font-size: 12px; padding: 6px 12px; border-radius: 6px;
            background: rgba(16,185,129,.1); color: #10b981;
            border: 1px solid rgba(16,185,129,.25);
            animation: dn-fade-in .2s ease;
        }
        @keyframes dn-fade-in { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:none; } }

        /* ── Notes list ───────────────────────────────────────────────── */
        .dn-list { padding: 0 20px 20px; }

        .dn-empty {
            text-align: center; padding: 40px 20px;
            font-size: 13px; color: #9ca3af;
        }

        .dn-note {
            margin-top: 16px; border-radius: 12px; overflow: hidden;
            border: 1px solid;
            transition: box-shadow .2s;
        }
        .dn-note:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .dn-note { background: #f9fafb; border-color: rgba(0,0,0,.07); }
        .dark .dn-note { background: #1e293b; border-color: rgba(255,255,255,.07); }
        .dark .dn-note:hover { box-shadow: 0 4px 20px rgba(0,0,0,.35); }

        .dn-note-header {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 14px;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        .dark .dn-note-header { border-color: rgba(255,255,255,.05); }

        .dn-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0;
            border: 2px solid;
        }

        .dn-author { font-size: 13px; font-weight: 600; }
        .dn-author { color: #111827; }
        .dark .dn-author { color: #f3f4f6; }

        .dn-role-badge {
            font-size: 10px; font-weight: 600; padding: 2px 7px;
            border-radius: 20px; border: 1px solid;
            text-transform: uppercase; letter-spacing: .04em;
        }

        .dn-time { font-size: 11px; color: #9ca3af; margin-left: auto; }

        .dn-visibility {
            font-size: 10px; font-weight: 600; padding: 2px 7px;
            border-radius: 20px; border: 1px solid;
        }
        .dn-visibility.public {
            background: rgba(16,185,129,.1); color: #10b981; border-color: rgba(16,185,129,.25);
        }
        .dn-visibility.internal {
            background: rgba(245,158,11,.1); color: #f59e0b; border-color: rgba(245,158,11,.25);
        }

        .dn-note-body {
            padding: 12px 14px;
            font-size: 13px; line-height: 1.65; white-space: pre-wrap; word-break: break-word;
        }
        .dn-note-body { color: #374151; }
        .dark .dn-note-body { color: #d1d5db; }

        /* Image notes */
        .dn-note-img {
            display: block; width: 100%; max-height: 360px; object-fit: cover;
            cursor: zoom-in;
            transition: opacity .15s;
        }
        .dn-note-img:hover { opacity: .9; }

        /* ── Lightbox ─────────────────────────────────────────────────── */
        .dn-lightbox {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(0,0,0,.88); backdrop-filter: blur(6px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            animation: dn-fade-in .15s ease;
            cursor: zoom-out;
        }
        .dn-lightbox img {
            max-width: 100%; max-height: 90dvh;
            border-radius: 10px;
            box-shadow: 0 32px 80px rgba(0,0,0,.7);
            object-fit: contain;
            cursor: default;
            animation: dn-modal-in .2s cubic-bezier(.175,.885,.32,1.275);
        }
        .dn-lightbox-close {
            position: absolute; top: 16px; right: 20px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
            color: #fff; font-size: 20px; width: 36px; height: 36px;
            border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .dn-lightbox-close:hover { background: rgba(255,255,255,.2); }

        .dn-note-footer {
            display: flex; justify-content: flex-end;
            padding: 6px 14px 10px;
        }

        .dn-delete-btn {
            font-size: 11px; padding: 3px 10px; border-radius: 6px;
            border: 1px solid rgba(239,68,68,.25); background: transparent;
            color: #ef4444; cursor: pointer;
            transition: background .15s;
        }
        .dn-delete-btn:hover { background: rgba(239,68,68,.08); }

        /* ── Photo QR modal ───────────────────────────────────────────── */
        .dn-modal-overlay {
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.65); backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            animation: dn-fade-in .2s ease;
        }

        .dn-modal {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 28px 24px;
            width: 100%; max-width: 360px;
            text-align: center;
            box-shadow: 0 24px 80px rgba(0,0,0,.6);
            animation: dn-modal-in .25s cubic-bezier(.175,.885,.32,1.275);
        }
        @keyframes dn-modal-in {
            from { transform: scale(.92) translateY(12px); opacity: 0; }
            to   { transform: none; opacity: 1; }
        }

        .dn-modal-close {
            float: right; background: none; border: none; color: #64748b;
            font-size: 20px; cursor: pointer; line-height: 1; margin-top: -4px;
        }
        .dn-modal-close:hover { color: #f1f5f9; }

        .dn-modal-title {
            font-size: 16px; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; clear: both;
        }
        .dn-modal-sub {
            font-size: 12px; color: #64748b; margin-bottom: 20px; line-height: 1.5;
        }

        .dn-qr-wrap {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 12px; border-radius: 12px; background: #fff;
            margin-bottom: 16px;
        }

        .dn-url-box {
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px; padding: 8px 12px;
            margin-bottom: 16px;
        }
        .dn-url-text {
            flex: 1; font-size: 11px; color: #94a3b8;
            word-break: break-all; text-align: left;
        }
        .dn-copy-btn {
            flex-shrink: 0; background: none; border: none;
            color: #6366f1; font-size: 13px; cursor: pointer; font-weight: 600;
        }
        .dn-copy-btn:hover { color: #818cf8; }

        .dn-refresh-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
            background: rgba(99,102,241,.15); color: #818cf8;
            border: 1px solid rgba(99,102,241,.25); cursor: pointer;
            transition: background .15s;
        }
        .dn-refresh-btn:hover { background: rgba(99,102,241,.25); }

        .dn-modal-note {
            font-size: 11px; color: #475569; margin-top: 14px; line-height: 1.5;
        }
    </style>

    @php
        $notes = $this->getNotes();

        $roleStyle = [
            'admin'    => ['bg' => 'rgba(99,102,241,.15)',  'border' => 'rgba(99,102,241,.4)',  'color' => '#6366f1'],
            'employee' => ['bg' => 'rgba(16,185,129,.15)',  'border' => 'rgba(16,185,129,.4)',  'color' => '#10b981'],
            'customer' => ['bg' => 'rgba(245,158,11,.15)',  'border' => 'rgba(245,158,11,.4)',  'color' => '#f59e0b'],
            'partner'  => ['bg' => 'rgba(236,72,153,.15)',  'border' => 'rgba(236,72,153,.4)',  'color' => '#ec4899'],
        ];

        $roleLabel = [
            'admin'    => 'Admin',
            'employee' => 'Mitarbeiter',
            'customer' => 'Kunde',
            'partner'  => 'Partner',
        ];
    @endphp

    {{-- Lightbox overlay (pure JS, no Livewire round-trip needed) --}}
    <div id="dn-lightbox" class="dn-lightbox" style="display:none;" onclick="dnCloseLightbox()">
        <button class="dn-lightbox-close" onclick="event.stopPropagation(); dnCloseLightbox()">✕</button>
        <img id="dn-lightbox-img" src="" alt="Foto" onclick="event.stopPropagation()">
    </div>

    {{-- QR / photo upload modal --}}
    @if($showPhotoModal)
        <div class="dn-modal-overlay" wire:click.self="closePhotoModal">
            <div class="dn-modal">
                <button class="dn-modal-close" wire:click="closePhotoModal">✕</button>

                <div class="dn-modal-title">📷 Foto via Telefon hochladen</div>
                <div class="dn-modal-sub">
                    Scanne den QR-Code mit deinem Telefon.<br>
                    Der Link verfällt nach dem ersten Upload.
                </div>

                {{-- QR code — generated server-side as an image, no JS needed --}}
                <div class="dn-qr-wrap">
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&ecc=M&data={{ urlencode($photoUploadUrl) }}"
                        width="200" height="200"
                        alt="QR Code"
                        style="display:block; border-radius:4px;"
                    >
                </div>

                {{-- Raw URL + copy --}}
                <div class="dn-url-box">
                    <span class="dn-url-text" id="dn-url-text">{{ $photoUploadUrl }}</span>
                    <button class="dn-copy-btn" onclick="
                        navigator.clipboard.writeText('{{ $photoUploadUrl }}');
                        this.textContent='✓';
                        setTimeout(()=>this.textContent='Kopieren',1500);
                    ">Kopieren</button>
                </div>

                <button class="dn-refresh-btn" wire:click="refreshPhotoLink">
                    ↺ Neuen Link generieren
                </button>

                <div class="dn-modal-note">
                    Nach dem Hochladen wird das Foto als interne Notiz gespeichert<br>
                    und ein neuer Link automatisch generiert.
                </div>
            </div>
        </div>

    @endif

    <div class="dn-wrap">

        {{-- Header --}}
        <div class="dn-header">
            <div class="dn-header-icon">💬</div>
            <span class="dn-header-title">Notizen & Kommentare</span>
            <span class="dn-count-badge">{{ $notes->count() }}</span>

            <button class="dn-photo-btn" wire:click="openPhotoModal">
                📷 Foto via Telefon
            </button>
        </div>

        {{-- Compose --}}
        <div class="dn-compose">

            @if($flashMessage)
                <div class="dn-flash" style="margin-bottom:10px;">✓ {{ $flashMessage }}</div>
            @endif

            <textarea
                wire:model="content"
                class="dn-compose-textarea"
                placeholder="Notiz hinzufügen…"
                rows="3"
            ></textarea>

            <div class="dn-compose-footer">
                {{-- Public toggle --}}
                <label class="dn-toggle-wrap">
                    <button
                        type="button"
                        class="dn-toggle {{ $is_public ? 'on' : 'off' }}"
                        wire:click="$toggle('is_public')"
                    ></button>
                    <span class="dn-toggle-label">
                        {{ $is_public ? 'Öffentlich (für Kunde sichtbar)' : 'Intern (nur Mitarbeiter)' }}
                    </span>
                </label>

                <button
                    type="button"
                    class="dn-submit"
                    wire:click="postNote"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                >
                    <span wire:loading.remove wire:target="postNote">✉ Senden</span>
                    <span wire:loading wire:target="postNote">…</span>
                </button>
            </div>

            @error('content')
                <div style="color:#ef4444; font-size:12px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Notes list --}}
        <div class="dn-list">

            @forelse($notes as $note)
                @php
                    $rs = $roleStyle[$note->author_role] ?? $roleStyle['admin'];
                    $initial = strtoupper(mb_substr($note->author_name, 0, 1));
                @endphp

                <div class="dn-note">
                    <div class="dn-note-header">

                        {{-- Avatar --}}
                        <div class="dn-avatar"
                             style="background:{{ $rs['bg'] }}; border-color:{{ $rs['border'] }}; color:{{ $rs['color'] }};">
                            {{ $note->type === 'image' ? '📷' : $initial }}
                        </div>

                        <span class="dn-author">{{ $note->author_name }}</span>

                        <span class="dn-role-badge"
                              style="background:{{ $rs['bg'] }}; color:{{ $rs['color'] }}; border-color:{{ $rs['border'] }};">
                            {{ $note->type === 'image' ? 'Foto' : ($roleLabel[$note->author_role] ?? $note->author_role) }}
                        </span>

                        <span class="dn-visibility {{ $note->is_public ? 'public' : 'internal' }}">
                            {{ $note->is_public ? '🌐 Öffentlich' : '🔒 Intern' }}
                        </span>

                        <span class="dn-time" title="{{ $note->created_at->format('d.m.Y H:i') }}">
                            {{ $note->created_at->diffForHumans() }}
                        </span>
                    </div>

                    @if($note->type === 'image')
                        <img
                            src="{{ Storage::url($note->content) }}"
                            alt="Gerät Foto"
                            class="dn-note-img"
                            loading="lazy"
                            onclick="dnOpenLightbox('{{ Storage::url($note->content) }}')"
                        >
                    @else
                        <div class="dn-note-body">{{ $note->content }}</div>
                    @endif

                    <div class="dn-note-footer">
                        <button
                            class="dn-delete-btn"
                            wire:click="deleteNote({{ $note->id }})"
                            wire:confirm="Notiz wirklich löschen?"
                        >
                            🗑 Löschen
                        </button>
                    </div>
                </div>
            @empty
                <div class="dn-empty">
                    <div style="font-size:32px; margin-bottom:8px;">📝</div>
                    <div>Noch keine Notizen — füge die erste hinzu.</div>
                </div>
            @endforelse

        </div>
    </div>

    <script>
        function dnOpenLightbox(src) {
            const lb  = document.getElementById('dn-lightbox');
            const img = document.getElementById('dn-lightbox-img');
            img.src = src;
            lb.style.display = 'flex';
            document.addEventListener('keydown', dnLightboxKeyHandler);
        }
        function dnCloseLightbox() {
            document.getElementById('dn-lightbox').style.display = 'none';
            document.getElementById('dn-lightbox-img').src = '';
            document.removeEventListener('keydown', dnLightboxKeyHandler);
        }
        function dnLightboxKeyHandler(e) {
            if (e.key === 'Escape') dnCloseLightbox();
        }
    </script>
</x-filament-widgets::widget>
