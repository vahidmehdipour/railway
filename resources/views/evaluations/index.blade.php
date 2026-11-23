@extends('layouts.app')

@section('title', 'ارزیابی‌ها')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">ارزیابی‌های پرسنل</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">ثبت، ویرایش و خروجی گرفتن از امتیازات پرسنل.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('evaluations.export', $filters) }}" class="inline-flex size-10 items-center justify-center rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300 transition hover:bg-emerald-100 dark:hover:bg-emerald-900/60" title="خروجی اکسل" aria-label="خروجی اکسل">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span class="sr-only">خروجی اکسل</span>
                </a>
                @can('create_evaluations')
                    <a href="{{ route('evaluations.create') }}" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        ثبت ارزیابی
                    </a>
                @endcan
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="تعداد ارزیابی" :value="number_format((int) ($stats->evaluations_count ?? 0))" tone="indigo" />
            <x-stat-card label="میانگین امتیاز" :value="number_format((float) ($stats->average_score ?? 0), 2)" tone="sky" />
            <x-stat-card label="بالاترین امتیاز" :value="number_format((float) ($stats->highest_score ?? 0), 2)" tone="emerald" />
            <x-stat-card label="پایین‌ترین امتیاز" :value="number_format((float) ($stats->lowest_score ?? 0), 2)" tone="amber" />
        </div>

        {{-- Filters --}}
        <div class="card p-4">
            <form method="GET" action="{{ route('evaluations.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="search" class="form-label">جست‌وجوی پرسنل</label>
                    <input id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="نام یا کد ملی..."
                        class="form-input">
                </div>

                <div>
                    <label for="period_id" class="form-label">دوره ارزیابی</label>
                    <select id="period_id" name="period_id" class="form-select">
                        <option value="">همه دوره‌ها</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((int) ($filters['period_id'] ?? 0) === $period->id)>{{ $period->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="personnel_id" class="form-label">پرسنل</label>
                    <select id="personnel_id" name="personnel_id" class="form-select">
                        <option value="">همه پرسنل</option>
                        @foreach ($personnels as $personnel)
                            <option value="{{ $personnel->id }}" @selected((int) ($filters['personnel_id'] ?? 0) === $personnel->id)>{{ $personnel->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">اعمال فیلتر</button>
                    @if (collect($filters)->filter()->isNotEmpty())
                        <a href="{{ route('evaluations.index') }}" class="btn-secondary" title="پاک کردن فیلترها">پاک‌سازی</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست ارزیابی‌ها</h3>
                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ number_format($evaluations->total()) }} مورد</span>
            </div>

            @if ($evaluations->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-sm font-bold text-slate-800 dark:text-slate-100">ارزیابی‌ای یافت نشد</h4>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">فیلترها را تغییر دهید یا ارزیابی جدیدی ثبت کنید.</p>
                    <a href="{{ route('evaluations.create') }}" class="btn-primary mt-5">ثبت ارزیابی</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                            <tr>
                                <th class="table-th">پرسنل</th>
                                <th class="table-th">دوره</th>
                                <th class="table-th">تاریخ (شمسی)</th>
                                <th class="table-th">تئوری</th>
                                <th class="table-th">عملی</th>
                                <th class="table-th">اخلاق</th>
                                <th class="table-th">موارد منفی</th>
                                <th class="table-th">امتیاز کل</th>
                                <th class="table-th text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($evaluations as $evaluation)
                                @php
                                    $total = $evaluation->calculatedTotal();
                                    $scoreTone = match (true) {
                                        $total >= $maxTotal * 0.8 => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-emerald-100 dark:ring-emerald-800',
                                        $total >= $maxTotal * 0.5 => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 ring-amber-100',
                                        default => 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 ring-rose-100 dark:ring-rose-800',
                                    };
                                @endphp
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                    <td class="table-td">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-xs font-bold text-white">
                                                {{ mb_substr($evaluation->personnel?->first_name ?? '?', 0, 1) }}
                                            </span>
                                            <div>
                                                <p class="font-semibold text-slate-800 dark:text-slate-100">{{ $evaluation->personnel?->full_name }}</p>
                                                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $evaluation->personnel?->role?->name_fa }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">{{ $evaluation->period?->title }}</td>
                                    <td class="table-td whitespace-nowrap" dir="rtl">{{ jdate($evaluation->evaluation_date) }}</td>
                                    <td class="table-td" dir="rtl">
                                        {{ $evaluation->theory_score !== null ? fa_num(number_format((float) $evaluation->theory_score, 2)) : '---' }}
                                    </td>
                                    <td class="table-td" dir="rtl">
                                        {{ $evaluation->practical_score !== null ? fa_num(number_format((float) $evaluation->practical_score, 2)) : '---' }}
                                    </td>
                                    <td class="table-td" dir="rtl">
                                        {{ $evaluation->ethics_score !== null ? fa_num(number_format((float) $evaluation->ethics_score, 2)) : '---' }}
                                    </td>
                                    <td class="table-td">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <span class="badge bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300" title="سانحهها">{{ fa_num($evaluation->accident_count) }} سانحه</span>
                                                <span class="badge bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300" title="شبه سانحهها">{{ fa_num($evaluation->near_miss_count) }} شبه سانحه</span>
                                                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300" title="اخطارها">{{ fa_num($evaluation->warning_count) }} اخطار</span>
                                            </div>
                                            <span class="text-xs font-medium text-rose-500" dir="rtl">
                                                {{ fa_num($evaluation->penaltyTotal() > 0 ? '−'.number_format($evaluation->penaltyTotal(), 2) : '0.00') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="table-td">
                                        <span class="badge text-sm font-bold ring-1 {{ $scoreTone }}">
                                            {{ fa_num(number_format($total, 2)) }}
                                        </span>
                                    </td>
                                    <td class="table-td">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('edit_evaluations')
                                                <a href="{{ route('evaluations.edit', $evaluation) }}" class="flex size-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700" title="ویرایش">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('delete_evaluations')
                                                <form method="POST" action="{{ route('evaluations.destroy', $evaluation) }}" onsubmit="return confirm('آیا از حذف این ارزیابی مطمئن هستید؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex size-9 items-center justify-center rounded-lg border border-rose-100 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300 transition hover:bg-rose-100 dark:hover:bg-rose-900/60" title="حذف">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($evaluations->hasPages())
                    <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-4">
                        {{ $evaluations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
