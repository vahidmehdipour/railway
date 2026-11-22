@extends('layouts.app')

@section('title', 'داشبورد')

@section('content')
    <div class="space-y-6">
        {{-- Welcome banner --}}
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-l from-indigo-700 via-indigo-600 to-sky-500 px-6 py-8 text-white shadow-xl shadow-indigo-200/60 sm:px-8">
            <div class="absolute -left-10 -top-10 size-48 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -bottom-16 right-10 size-56 rounded-full bg-sky-300/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-100">خوش آمدید، {{ auth()->user()->name }}</p>
                    <h2 class="mt-1 text-2xl font-bold sm:text-3xl">مدیریت امتیازات پرسنل راه‌آهن</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-indigo-100">
                        پرسنل را مدیریت کنید، ارزیابی‌های دوره‌ای را ثبت کنید و عملکرد نفرات برتر را در یک نگاه ببینید.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @can('create_evaluations')
                        <a href="{{ route('evaluations.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-bold text-indigo-700 dark:text-indigo-300 shadow-sm transition hover:bg-indigo-50 dark:hover:bg-indigo-900/40">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            ثبت ارزیابی جدید
                        </a>
                    @endcan
                    <a href="{{ route('personnels.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">
                        مدیریت پرسنل
                    </a>
                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="کل پرسنل" :value="number_format($totalPersonnel)" :hint="$activePersonnel.' نفر فعال'" tone="indigo">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="کل ارزیابی‌ها" :value="number_format($totalEvaluations)" hint="ثبت‌شده در سیستم" tone="emerald">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="دوره‌های ارزیابی" :value="number_format($totalPeriods)" hint="دوره‌های تعریف‌شده" tone="amber">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card label="دوره‌های فعال" :value="number_format($activePeriods->count())" hint="در حال اجرا" tone="sky">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </section>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- Top personnel --}}
            <section class="card overflow-hidden xl:col-span-2">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">۵ نفر برتر بر اساس امتیاز</h3>
                        <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">امتیاز نهایی محاسبه‌شده از میانگین ارزیابی‌ها و کسر جریمه‌ها</p>
                    </div>
                    <a href="{{ route('evaluations.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-300 hover:text-indigo-500">مشاهده همه</a>
                </div>

                @if ($topPersonnel->isEmpty())
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm text-slate-500 dark:text-slate-400">هنوز ارزیابی‌ای ثبت نشده است.</p>
                        <a href="{{ route('evaluations.create') }}" class="mt-3 inline-flex text-sm font-semibold text-indigo-600 dark:text-indigo-300 hover:text-indigo-500">ثبت اولین ارزیابی</a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                                <tr>
                                    <th class="table-th w-16">رتبه</th>
                                    <th class="table-th">پرسنل</th>
                                    <th class="table-th">نقش</th>
                                    <th class="table-th">تعداد ارزیابی</th>
                                    <th class="table-th">امتیاز نهایی</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($topPersonnel as $index => $row)
                                    <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                        <td class="table-td">
                                            @php
                                                $rankClasses = match ($index) {
                                                    0 => 'bg-amber-100 text-amber-700 dark:text-amber-300 ring-amber-200',
                                                    1 => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 ring-slate-200 dark:ring-slate-700',
                                                    2 => 'bg-orange-100 text-orange-700 ring-orange-200',
                                                    default => 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 ring-slate-100 dark:ring-slate-700',
                                                };
                                            @endphp
                                            <span class="inline-flex size-8 items-center justify-center rounded-full text-xs font-bold ring-1 {{ $rankClasses }}">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex items-center gap-3">
                                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-xs font-bold text-indigo-600 dark:text-indigo-300">
                                                    {{ mb_substr($row->first_name ?? '?', 0, 1) }}
                                                </span>
                                                <div>
                                                    <a href="{{ route('personnels.show', $row) }}" class="font-semibold text-slate-800 dark:text-slate-100 transition hover:text-indigo-600">{{ $row->full_name }}</a>
                                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $row->national_code ?? 'بدون کد ملی' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="table-td">{{ $row->role?->name_fa }}</td>
                                        <td class="table-td">{{ number_format($row->evaluations_count) }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-100 dark:ring-emerald-800">
                                                {{ number_format($row->final_score, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            {{-- Side column --}}
            <div class="space-y-6">
                <section class="card p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">دوره‌های فعال</h3>
                        <a href="{{ route('periods.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-300 hover:text-indigo-500">مدیریت</a>
                    </div>

                    <div class="mt-4 space-y-3">
                        @forelse ($activePeriods as $period)
                            <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/60 p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $period->title }}</p>
                                    <span class="badge bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">فعال</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" dir="ltr">
                                    {{ jdate($period->start_date) }} - {{ jdate($period->end_date) }}
                                </p>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 px-3 py-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                دوره فعالی وجود ندارد.
                            </p>
                        @endforelse
                    </div>
                </section>

                <section class="card p-5">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">آخرین ارزیابی‌ها</h3>

                    <div class="mt-4 space-y-3">
                        @forelse ($recentEvaluations as $evaluation)
                            <div class="flex items-center gap-3">
                                <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-xs font-bold text-slate-600 dark:text-slate-300">
                                    {{ mb_substr($evaluation->personnel?->first_name ?? '?', 0, 1) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('personnels.show', $evaluation->personnel_id) }}" class="block truncate text-sm font-medium text-slate-800 dark:text-slate-100 transition hover:text-indigo-600">{{ $evaluation->personnel?->full_name }}</a>
                                    <p class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $evaluation->period?->title }} — {{ jdate($evaluation->evaluation_date) }}</p>
                                </div>
                                <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ number_format($evaluation->calculatedTotal(), 2) }}</span>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 px-3 py-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                ارزیابی‌ای ثبت نشده است.
                            </p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
