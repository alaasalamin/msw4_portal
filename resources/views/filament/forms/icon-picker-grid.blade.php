@php($choices = \App\Models\ObjectType::ICON_CHOICES)

<div
    x-data="{
        q: '',
        pick(icon) {
            $wire.set('data.icon', icon);
            $wire.unmountAction();
        },
        match(label, name) {
            const q = this.q.trim().toLowerCase();
            if (! q) return true;
            return label.toLowerCase().includes(q)
                || name.toLowerCase().includes(q);
        },
    }"
    style="display:flex; flex-direction:column; gap:14px;"
>
    {{-- Search --}}
    <label style="position:relative; display:block;">
        <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--gray-400, #9ca3af); pointer-events:none;">
            @svg('heroicon-o-magnifying-glass', ['style' => 'width:18px; height:18px;'])
        </span>
        <input
            type="text"
            x-model="q"
            x-init="$nextTick(() => $el.focus())"
            placeholder="Search icons…"
            style="
                width:100%;
                padding:10px 12px 10px 38px;
                border:1px solid var(--gray-200, #e5e7eb);
                border-radius:10px;
                font:inherit;
                font-size:14px;
                outline:none;
                background:var(--color-white, #ffffff);
                color:var(--gray-900, #111827);
            "
            onfocus="this.style.borderColor='var(--primary-500, #4f46e5)'; this.style.boxShadow='0 0 0 3px var(--primary-100, rgba(79,70,229,.15))';"
            onblur="this.style.borderColor='var(--gray-200, #e5e7eb)'; this.style.boxShadow='';"
        />
    </label>

    {{-- Grid --}}
    <div
        style="
            display:grid;
            grid-template-columns:repeat(6, minmax(0, 1fr));
            gap:10px;
            max-height:60vh;
            overflow-y:auto;
            padding:4px 2px;
        "
    >
        @foreach ($choices as $icon => $label)
            <button
                type="button"
                x-show="match('{{ addslashes($label) }}', '{{ addslashes($icon) }}')"
                x-on:click="pick('{{ $icon }}')"
                title="{{ $label }}"
                style="
                    display:flex;
                    flex-direction:column;
                    align-items:center;
                    justify-content:center;
                    gap:6px;
                    padding:14px 8px;
                    border:1px solid var(--gray-200, #e5e7eb);
                    border-radius:12px;
                    background:var(--color-white, #ffffff);
                    cursor:pointer;
                    transition:transform .15s ease, border-color .15s ease, box-shadow .15s ease;
                    color:var(--gray-700, #374151);
                    font:inherit;
                "
                onmouseover="this.style.borderColor='var(--primary-400, #6366f1)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)';"
                onmouseout="this.style.borderColor='var(--gray-200, #e5e7eb)'; this.style.transform=''; this.style.boxShadow='';"
            >
                <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px;">
                    @svg($icon, ['style' => 'width:28px; height:28px;'])
                </span>
                <span style="
                    font-size:10px;
                    line-height:1.2;
                    color:var(--gray-500, #6b7280);
                    text-align:center;
                    overflow:hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap;
                    max-width:100%;
                ">
                    {{ $label }}
                </span>
            </button>
        @endforeach
    </div>
</div>
