@extends('layouts.app')

@section('title', 'پیشنمایش وارد کردن پرسنل')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">پیشنمایش وارد کردن پرسنل</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                فایل <span class="font-semibold text-slate-700 dark:text-slate-200" dir="auto">{{ $import['filename'] }}</span>
                بررسی شد. تا زمانی که دکمه ذخیره را نزنید، هیچ اطلاعاتی تغییر نخواهد کرد.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <x-stat-card label="ردیفها" :value="fa_num($import['summary']['total'])" tone="indigo" />
            <x-stat-card label="پرسنل جدید" :value="fa_num($import['summary']['new'])" tone="emerald" />
            <x-stat-card label="پرسنل موجود" :value="fa_num($import['summary']['existing'])" tone="amber" />
            <x-stat-card label="خطاها" :value="fa_num($import['summary']['errors'])" tone="sky" />
        </div>

        @if ($import['summary']['errors'] > 0)
            <div class="rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 px-5 py-4 text-sm text-rose-700 dark:text-rose-200">
                <p class="font-bold">{{ fa_num($import['summary']['errors']) }} ردیف دارای خطا است و وارد نخواهد شد:</p>
                <ul class="mt-2 list-disc pr-5 space-y-1">
                    @foreach ($import['rows'] as $row)
                        @foreach ($row['errors'] as $error)
                            <li>ردیف {{ fa_num($row['row']) }} ({{ $row['label'] ?: 'بدون نام' }}): {{ $error }}</li>
                        @endforeach
                    @endforeach
                </ul>
                <p class="mt-3 text-xs">پس از اصلاح فایل، دوباره آن را بارگذاری کنید.</p>
            </div>
        @endif

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست ردیفهای قابل وارد کردن</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        حالت: {{ $import['mode'] === 'scores' ? 'پرسنل و امتیاز ارزیابی' : 'فقط اطلاعات پرسنل' }}
                    </p>
                </div>
                <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ fa_num($import['summary']['total']) }} ردیف</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                    <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                        <tr>
                            <th class="table-th">ردیف</th>
                            <th class="table-th">پرسنل</th>
                            <th class="table-th">نقش</th>
                            <th class="table-th">کد ملی</th>
                            <th class="table-th">وضعیت</th>
                            @if ($import['mode'] === 'scores')
                                <th class="table-th">تاریخ ارزیابی</th>
                                <th class="table-th">دوره / ایستگاه</th>
                                <th class="table-th">امتیازها</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach ($import['rows'] as $row)
                            <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60 {{ $row['errors'] !== [] ? 'bg-rose-50/40 dark:bg-rose-950/20' : '' }}">
                                <td class="table-td">{{ fa_num($row['row']) }}</td>
                                <td class="table-td font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $row['label'] ?: '—' }}
                                    <span class="block text-xs font-normal text-slate-400 dark:text-slate-500">{{ fa_num($row['person']['entry_year']) }}</span>
                                </td>
                                <td class="table-td">{{ $row['display']['role'] ?: '—' }}</td>
                                <td class="table-td" dir="rtl">{{ $row['display']['national_code'] !== '' ? fa_num($row['display']['national_code']) : '—' }}</td>
                                <td class="table-td">
                                    @if ($row['errors'] !== [])
                                        <span class="badge bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300">خطا</span>
                                    @elseif ($row['status'] === 'existing')
                                        <span class="badge bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">پرسنل موجود</span>
                                    @else
                                        <span class="badge bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">جدید</span>
                                    @endif
                                </td>
                                @if ($import['mode'] === 'scores')
                                    <td class="table-td whitespace-nowrap">{{ $row['display']['date'] !== '' ? fa_num($row['display']['date']) : '—' }}</td>
                                    <td class="table-td">
                                        <span class="block">{{ $row['display']['period'] ?: '—' }}</span>
                                        @if ($row['display']['station'] !== '')
                                            <span class="block text-xs text-slate-400 dark:text-slate-500">{{ $row['display']['station'] }}</span>
                                        @endif
                                    </td>
                                    <td class="table-td whitespace-nowrap text-xs">
                                        @if ($row['scores'] !== null)
                                            {{ fa_num($row['scores']['theory_score'] ?? '—') }} /
                                            {{ fa_num($row['scores']['practical_score'] ?? '—') }} /
                                            {{ fa_num($row['scores']['ethics_score'] ?? '—') }}
                                            <span class="block text-rose-500">
                                                {{ fa_num($row['scores']['accident_count']) }} سانحه،
                                                {{ fa_num($row['scores']['near_miss_count']) }} شبه سانحه،
                                                {{ fa_num($row['scores']['warning_count']) }} اخطار
                                            </span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('settings.import.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $import['token'] }}">
                <button type="submit" class="btn-primary justify-center" @disabled($import['summary']['errors'] > 0)>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    تأیید و ذخیره {{ fa_num($import['summary']['total'] - $import['summary']['errors']) }} ردیف
                </button>
            </form>
            <a href="{{ route('settings.import.index') }}" class="btn-secondary justify-center">انصراف و بارگذاری فایل دیگر</a>
        </div>
    </div>
@endsection
