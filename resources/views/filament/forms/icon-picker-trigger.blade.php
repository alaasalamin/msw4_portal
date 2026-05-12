@php
    $icon  = $icon  ?? 'heroicon-o-cube';
    $label = $label ?? 'Choose icon';
@endphp

<span style="display:flex; flex-direction:column; align-items:center; gap:10px; padding:8px 4px;">
    <span style="
        position:relative;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:96px;
        height:96px;
        color:var(--gray-700, #374151);
    ">
        @svg($icon, ['style' => 'width:80px; height:80px;'])

        {{-- "Change" badge: pencil-square in a circle, bottom-right --}}
        <span style="
            position:absolute;
            bottom:-2px;
            right:-2px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:28px;
            height:28px;
            border-radius:9999px;
            background:var(--primary-600, #4f46e5);
            color:#ffffff;
            box-shadow:0 0 0 3px var(--color-white, #ffffff), 0 2px 6px rgba(0,0,0,.12);
        ">
            @svg('heroicon-m-pencil-square', ['style' => 'width:14px; height:14px;'])
        </span>
    </span>
    <span style="
        font-size:12px;
        line-height:1.2;
        font-weight:600;
        color:var(--gray-600, #4b5563);
        text-align:center;
        max-width:140px;
    ">
        {{ $label }}
    </span>
</span>
