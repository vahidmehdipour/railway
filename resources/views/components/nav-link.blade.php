@props(['item'])

<a
    href="{{ route($item['route']) }}"
    @class([
        'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
        'bg-indigo-500/15 text-white ring-1 ring-inset ring-indigo-400/30' => $item['active'],
        'text-slate-400 hover:bg-white/5 hover:text-white' => ! $item['active'],
    ])
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"
        @class(['size-5 shrink-0', 'text-indigo-300' => $item['active'], 'text-slate-500 dark:text-slate-400 group-hover:text-slate-300' => ! $item['active']])>
        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
    </svg>
    <span>{{ $item['label'] }}</span>
    @if ($item['active'])
        <span class="mr-auto size-1.5 rounded-full bg-indigo-400"></span>
    @endif
</a>
