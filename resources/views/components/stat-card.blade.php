@props(['label', 'value', 'hint' => null, 'tone' => 'indigo'])

@php
    $tones = [
        'indigo' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 ring-indigo-100 dark:ring-indigo-800',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300 ring-emerald-100 dark:ring-emerald-800',
        'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 ring-amber-100',
        'sky' => 'bg-sky-50 text-sky-600 ring-sky-100',
    ];
@endphp

<div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm transition hover:shadow-md">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $value }}</p>
            @if ($hint)
                <p class="mt-2 text-xs text-slate-400 dark:text-slate-500">{{ $hint }}</p>
            @endif
        </div>

        @isset($icon)
            <div class="flex size-12 shrink-0 items-center justify-center rounded-xl ring-1 {{ $tones[$tone] ?? $tones['indigo'] }}">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>
