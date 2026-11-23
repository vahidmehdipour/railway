@extends('layouts.app')

@section('title', 'تنظیمات سیستم')

@section('content')
    @php
        $sections = [
            [
                'title' => 'تنظیمات امتیازدهی',
                'description' => 'حداکثر نمرات و ضرایب کسر امتیاز',
                'route' => route('settings.scoring'),
                'tone' => 'indigo',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5h6m-6 0a2.25 2.25 0 100 4.5h6a2.25 2.25 0 000-4.5m-6 0v15m6-15v15m-6 0h6m-6 0a2.25 2.25 0 100 4.5h6a2.25 2.25 0 000-4.5" />',
            ],
            [
                'title' => 'ایستگاهها',
                'description' => 'تعریف و ویرایش ایستگاههای ارزیابی',
                'route' => route('settings.stations.index'),
                'tone' => 'emerald',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-3.75m7.5 3.75v-3.75M4.5 8.25h15m-16.5 0L3 18.75h18L17.25 8.25m0 0l-2.25-5.25H9.75L7.5 8.25m4.5 3v3m0 0h.008v.008H12V11.25z" />',
            ],
            [
                'title' => 'نقشهای پرسنل',
                'description' => 'سمتها و نقشهای قابل انتخاب',
                'route' => route('settings.roles.index'),
                'tone' => 'amber',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />',
            ],
            [
                'title' => 'وارد کردن پرسنل',
                'description' => 'دریافت اطلاعات از فایل اکسل',
                'route' => route('settings.import.index'),
                'tone' => 'sky',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />',
            ],
        ];

        $tones = [
            'indigo' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/60',
            'emerald' => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/60',
            'amber' => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/60',
            'sky' => 'bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-300 group-hover:bg-sky-100 dark:group-hover:bg-sky-900/60',
        ];
    @endphp

    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">تنظیمات سیستم</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                حداکثر نمرات، ضرایب کسر امتیاز و ایستگاههای قابل انتخاب در فرم ارزیابی را از این بخش مدیریت کنید.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ($sections as $section)
                <a href="{{ $section['route'] }}"
                    class="card group flex min-h-40 flex-col items-center justify-center gap-3 p-5 text-center transition duration-200 hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg dark:hover:border-indigo-700 sm:aspect-square">
                    <span class="flex size-14 items-center justify-center rounded-2xl transition {{ $tones[$section['tone']] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-7">
                            {!! $section['icon'] !!}
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $section['title'] }}</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $section['description'] }}</span>
                    </span>
                    <span class="mt-1 flex size-7 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition group-hover:bg-indigo-50 group-hover:text-indigo-600 dark:bg-slate-700/60 dark:text-slate-400 dark:group-hover:bg-indigo-900/40 dark:group-hover:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4 rotate-180">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
