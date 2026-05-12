<div style="padding: 4px 0;">
    @if ($records->isEmpty())
        <p style="font-size:14px; color:rgb(107 114 128); margin:0;">
            No {{ $type->name }} linked to this customer yet.
        </p>
    @else
        <ul style="list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px;">
            @foreach ($records as $record)
                <li style="border:1px solid rgb(229 231 235); border-radius:10px; padding:12px 14px; background:rgb(249 250 251);">
                    <div style="font-size:11px; font-weight:600; letter-spacing:.04em; color:rgb(107 114 128); text-transform:uppercase; margin-bottom:6px;">
                        #{{ $record->id }} · {{ $record->created_at?->format('Y-m-d') }}
                    </div>
                    @if (is_array($record->data) && count($record->data))
                        <dl style="display:grid; grid-template-columns:auto 1fr; gap:4px 12px; margin:0; font-size:13px;">
                            @foreach ($record->data as $key => $value)
                                @if ($value !== null && $value !== '')
                                    <dt style="color:rgb(107 114 128); font-weight:500;">{{ $key }}</dt>
                                    <dd style="margin:0; color:rgb(17 24 39);">
                                        {{ is_bool($value) ? ($value ? 'yes' : 'no') : $value }}
                                    </dd>
                                @endif
                            @endforeach
                        </dl>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
