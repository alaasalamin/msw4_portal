<div style="padding:4px 0;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px; font-size:12px; color:#6b7280;">
        <div>
            <span style="font-weight:600; color:#374151;">Form:</span>
            {{ $record->form?->name ?? '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#374151;">Page:</span>
            {{ $record->page_slug ?: '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#374151;">IP:</span>
            {{ $record->ip_address ?: '—' }}
        </div>
        <div>
            <span style="font-weight:600; color:#374151;">Submitted:</span>
            {{ $record->created_at->format('d M Y H:i') }}
        </div>
    </div>

    <div style="border-top:1px solid #f3f4f6; padding-top:14px;">
        @forelse ($record->data ?? [] as $label => $value)
            <div style="display:flex; flex-direction:column; margin-bottom:12px; padding:10px 12px; background:#f9fafb; border-radius:8px;">
                <span style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; margin-bottom:4px;">
                    {{ $label }}
                </span>
                <span style="font-size:13px; color:#111827; white-space:pre-wrap;">{{ $value }}</span>
            </div>
        @empty
            <p style="color:#9ca3af; font-size:13px; text-align:center; padding:20px 0;">No data recorded.</p>
        @endforelse
    </div>
</div>
