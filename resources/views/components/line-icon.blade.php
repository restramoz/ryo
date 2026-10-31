@props(['name'])

<svg {{ $attributes->merge(['class' => 'line-icon-svg', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.75', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('website')
            <rect x="3.25" y="4.25" width="17.5" height="15.5" rx="1.5"/><path d="M3.5 8.25h17M7 6.25h.01M10 6.25h.01M7 12h10m-10 3h6"/>
            @break
        @case('landing')
            <path d="m6 3 12.5 10.2-5.2 1.1-2.1 5.1L6 3Z"/><path d="m13.3 14.3 4.2 5.1"/>
            @break
        @case('commerce')
            <path d="M4 8h16l-1.2 12H5.2L4 8Z"/><path d="M8 8a4 4 0 0 1 8 0m-8 4v1m8-1v1"/>
            @break
        @case('application')
            <path d="m8.5 7-5 5 5 5m7-10 5 5-5 5M14 4l-4 16"/>
            @break
        @case('design')
            <path d="m12 3 8.5 5-8.5 5-8.5-5L12 3Z"/><path d="m4 12 8 4.8 8-4.8M4 16l8 5 8-5"/>
            @break
        @case('performance')
            <path d="M4 19V5m0 14h16M7.5 15.5l3.2-4 3 2 5-6"/><circle cx="18.7" cy="7.5" r="1.3"/>
            @break
        @case('education')
            <path d="M12 6.5C9.3 4.5 6.4 4 3.5 5v14c3-1 5.9-.5 8.5 1.5m0-14C14.7 4.5 17.6 4 20.5 5v14c-3-1-5.9-.5-8.5 1.5m0-14v14.5"/>
            @break
        @case('automation')
            <rect x="7" y="7" width="10" height="10" rx="2"/><path d="M10 3v4m4-4v4m-4 10v4m4-4v4M3 10h4m-4 4h4m10-4h4m-4 4h4"/><path d="m12 9 .8 2.2L15 12l-2.2.8L12 15l-.8-2.2L9 12l2.2-.8L12 9Z"/>
            @break
        @case('discover')
            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v7a2.5 2.5 0 0 1-2.5 2.5H10l-5 4v-4.3a2.5 2.5 0 0 1-1-2V5.5Z"/><path d="M8 8h8m-8 3h5"/>
            @break
        @case('strategy')
            <circle cx="12" cy="12" r="9"/><path d="m15.8 8.2-2.2 5.4-5.4 2.2 2.2-5.4 5.4-2.2Z"/><path d="M12 3v2m9 7h-2M12 21v-2m-9-7h2"/>
            @break
        @case('craft')
            <path d="m4 20 4.5-1 10.8-10.8a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z"/><path d="m14.8 7.2 3 3M4 20h6"/>
            @break
        @case('develop')
            <path d="m8.5 7-5 5 5 5m7-10 5 5-5 5M14 4l-4 16"/>
            @break
        @case('launch')
            <path d="M14 4c2.7-.9 5.1-.7 6-.5.2.9.4 3.3-.5 6-1.3 3.8-4.8 7.2-8.5 8.4l-4-4C8.2 10.2 11.3 5.5 14 4Z"/><path d="m7 13-3 .8L3 18l4.2-1m4.8 1 2 3 4-1.5-.6-3"/><circle cx="15.8" cy="8.2" r="1.5"/>
            @break
    @endswitch
</svg>
