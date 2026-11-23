@extends('layouts.app')

@section('title', 'پروفایل پرسنل')

@section('content')
    @php
        $maxTotal = (float) get_setting('max_theory_score', 14)
            + (float) get_setting('max_practical_score', 4)
            + (float) get_setting('max_ethics_score', 2);
        $totalPenalties = $personnel->evaluations->sum(fn ($evaluation) => $evaluation->penaltyTotal());
        $finalScoreTone = match (true) {
            $personnel->final_score >= $maxTotal * 0.8 => 'from-emerald-500 to-teal-600',
            $personnel->final_score >= $maxTotal * 0.5 => 'from-amber-500 to-orange-600',
            default => 'from-rose-500 to-rose-700',
        };
    @endphp

    <style>
        @media print {
            aside,
            header,
            footer,
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }

            main {
                padding: 0 !important;
            }

            .print-card {
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
            }
        }
    </style>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('personnels.index') }}" class="flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-700 no-print" aria-label="بازگشت">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $personnel->full_name }}</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        {{ $personnel->role?->name_fa ?? 'بدون نقش' }} — پروفایل و تاریخچه ارزیابی‌ها
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 no-print">
                <a href="{{ route('personnels.pdf', $personnel) }}" class="inline-flex size-10 items-center justify-center rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300 transition hover:bg-rose-100 dark:hover:bg-rose-900/60" title="خروجی PDF" aria-label="خروجی PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="sr-only">خروجی PDF</span>
                </a>
                <button type="button" onclick="window.print()" class="inline-flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700" title="چاپ" aria-label="چاپ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                    </svg>
                    <span class="sr-only">چاپ</span>
                </button>
                @can('edit_personnels')
                    <a href="{{ route('personnels.edit', $personnel) }}" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                        </svg>
                        ویرایش اطلاعات
                    </a>
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- Details --}}
            <section class="card print-card p-6 xl:col-span-2">
                <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">اطلاعات پرسنل</h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500">مشخصات پایه و وضعیت پرسنل در سیستم</p>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">نام و نام خانوادگی</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $personnel->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">نقش</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $personnel->role?->name_fa ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">سال ورود</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100" dir="rtl">{{ fa_num($personnel->entry_year) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">کد ملی</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100" dir="rtl">{{ $personnel->national_code ? fa_num($personnel->national_code) : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">وضعیت</dt>
                        <dd class="mt-1">
                            @if ($personnel->is_active)
                                <span class="badge bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-100 dark:ring-emerald-800">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    فعال
                                </span>
                            @else
                                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 ring-1 ring-slate-200 dark:ring-slate-700">
                                    <span class="size-1.5 rounded-full bg-slate-400"></span>
                                    غیرفعال
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-400 dark:text-slate-500">تعداد ارزیابی‌ها</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ fa_num(number_format($personnel->evaluations->count())) }} مورد</dd>
                    </div>
                </dl>
            </section>

            {{-- Final score --}}
            <section class="print-card overflow-hidden rounded-3xl bg-gradient-to-br {{ $finalScoreTone }} p-6 text-white shadow-xl">
                <p class="text-sm font-medium text-white/80">امتیاز نهایی پرسنل</p>

                <div class="mt-4 flex items-end justify-center gap-2">
                    <span class="text-6xl font-black tracking-tight">{{ fa_num(number_format($personnel->final_score, 2)) }}</span>
                    <span class="pb-2 text-sm text-white/70">از {{ fa_num(number_format($maxTotal, 2)) }}</span>
                </div>

                <p class="mt-3 text-center text-xs leading-6 text-white/80">
                    میانگین نمرات تئوری، عملی و اخلاق منهای مجموع جریمه‌های ثبت‌شده
                </p>

                <dl class="mt-6 space-y-2.5 border-t border-white/20 pt-5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-white/70">تعداد ارزیابی</dt>
                        <dd class="font-semibold">{{ fa_num(number_format($personnel->evaluations->count())) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-white/70">مجموع جریمه‌ها</dt>
                        <dd class="font-semibold">−{{ fa_num(number_format($totalPenalties, 2)) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-white/70">حداکثر امتیاز ممکن</dt>
                        <dd class="font-semibold">{{ fa_num(number_format($maxTotal, 2)) }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- Evaluation history --}}
        <section class="card print-card overflow-hidden">
            <div class="flex flex-col gap-2 border-b border-slate-100 dark:border-slate-700 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">تاریخچه ارزیابی‌ها</h3>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">همه ارزیابی‌های ثبت‌شده برای این پرسنل به ترتیب تاریخ</p>
                </div>
                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ fa_num(number_format($personnel->evaluations->count())) }} مورد</span>
            </div>

            @if ($personnel->evaluations->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-sm font-bold text-slate-800 dark:text-slate-100">ارزیابی‌ای ثبت نشده است</h4>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">برای این پرسنل هنوز هیچ ارزیابی ثبت نشده است.</p>
                    <a href="{{ route('evaluations.create', ['personnel_id' => $personnel->id]) }}" class="btn-primary mt-5 no-print">ثبت اولین ارزیابی</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                            <tr>
                                <th class="table-th">تاریخ (شمسی)</th>
                                <th class="table-th">ایستگاه</th>
                                <th class="table-th">تئوری</th>
                                <th class="table-th">عملی</th>
                                <th class="table-th">اخلاق</th>
                                <th class="table-th">موارد منفی</th>
                                <th class="table-th">امتیاز</th>
                                <th class="table-th">توضیحات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($personnel->evaluations as $evaluation)
                                @php
                                    $total = $evaluation->calculatedTotal();
                                    $scoreTone = match (true) {
                                        $total >= $maxTotal * 0.8 => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 ring-emerald-100 dark:ring-emerald-800',
                                        $total >= $maxTotal * 0.5 => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 ring-amber-100',
                                        default => 'bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 ring-rose-100 dark:ring-rose-800',
                                    };
                                @endphp
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                    <td class="table-td whitespace-nowrap font-semibold text-slate-700 dark:text-slate-200" dir="rtl">
                                        {{ jdate($evaluation->evaluation_date) }}
                                    </td>
                                    <td class="table-td whitespace-nowrap">{{ $evaluation->station?->name ?? '—' }}</td>
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
                                                <span class="badge bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300">{{ fa_num($evaluation->accident_count) }} سانحه</span>
                                                <span class="badge bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">{{ fa_num($evaluation->near_miss_count) }} شبه سانحه</span>
                                                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ fa_num($evaluation->warning_count) }} اخطار</span>
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
                                    <td class="table-td max-w-xs">
                                        @if ($evaluation->description)
                                            <p class="line-clamp-2 text-xs leading-5 text-slate-600 dark:text-slate-300" title="{{ $evaluation->description }}">
                                                {{ $evaluation->description }}
                                            </p>
                                        @else
                                            <span class="text-xs text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
