@props(['name'])

<svg {{ $attributes->class('size-5 shrink-0')->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('home') <path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10v9h13v-9M9 19v-5h6v5"/> @break
        @case('document') <path d="M6 3h9l3 3v15H6z"/><path d="M14 3v4h4M9 11h6M9 15h6"/> @break
        @case('folder') <path d="M3 6.5h7l2 2h9v10.5H3z"/> @break
        @case('tag') <path d="M20 13 13 20l-9-9V4h7z"/><circle cx="8.5" cy="8.5" r="1"/> @break
        @case('chat') <path d="M21 12a8 8 0 0 1-8 8H5l-2 2v-8a9 9 0 1 1 18-2Z"/> @break
        @case('photo') <rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m4 17 4-4 3 3 3-3 6 5"/> @break
        @case('bars') <path d="M4 6h16M4 12h16M4 18h16"/> @break
        @case('squares') <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/> @break
        @case('brush') <path d="m14 4 6 6-8.5 8.5a4.2 4.2 0 0 1-6-6z"/><path d="m12 6 6 6M8 17c-1 3-3 3-5 3 1-1 1-3 1-4"/> @break
        @case('puzzle') <path d="M8 3h4v3a2 2 0 1 0 4 0V3h5v6h-3a2 2 0 1 0 0 4h3v8h-7v-3a2 2 0 1 0-4 0v3H3v-7h3a2 2 0 1 0 0-4H3V3z"/> @break
        @case('megaphone') <path d="m3 11 15-6v14L3 13zM7 14v6h4l-1-5"/><path d="M21 10v4"/> @break
        @case('credit-card') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M7 15h3"/> @break
        @case('users') <path d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 20v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/> @break
        @case('mail') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/> @break
        @case('user') <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/> @break
        @case('cog') <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21h-4v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.6-1H3v-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6V3h4v.09A1.7 1.7 0 0 0 15 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.6 1h.09v4H21a1.7 1.7 0 0 0-1.6 1Z"/> @break
        @case('eye') <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/> @break
        @case('plus') <path d="M12 5v14M5 12h14"/> @break
        @case('logout') <path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/> @break
        @default <circle cx="12" cy="12" r="9"/>
    @endswitch
</svg>
