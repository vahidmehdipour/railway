@extends('layouts.app')

@section('title', 'لاگ سیستم')

@section('content')
    @php
        $modelLabels = [
            'Personnel' => 'پرسنل',
            'Evaluation' => 'ارزیابی',
            'Station' => 'ایستگاه',
            'Role' => 'نقش پرسنل',
            'Setting' => 'تنظیمات',
        ];
    @endphp

    <div class="space-y-6">
        <section class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">لاگ سیستم</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">کلیه تغییرات ثبتشده در پرسنل، ارزیابیها، ایستگاهها، نقشها و تنظیمات.</p>
            </div>
            <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ number_format($activities->total()) }} رویداد</span>
        </section>

        <section class="card overflow-hidden">
            @if ($activities->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                    هنوز هیچ رویدادی ثبت نشده است.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                            <tr>
                                <th class="table-th">تاریخ</th>
                                <th class="table-th">کاربر</th>
                                <th class="table-th">عملیات</th>
                                <th class="table-th">مدل</th>
                                <th class="table-th">تغییرات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($activities as $activity)
                                @php
                                    $isLogin = $activity->description === 'ورود به سیستم';
                                    $type = class_basename((string) $activity->subject_type);
                                    $attributes = $activity->properties->get('attributes', []);
                                    $ip = $activity->properties->get('ip');
                                    $browser = $activity->properties->get('browser');
                                @endphp
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                    <td class="table-td" dir="ltr">{{ jdate($activity->created_at, 'Y/m/d H:i:s') }}</td>
                                    <td class="table-td">
                                        <span class="font-medium">{{ $activity->causer?->name ?? 'سیستم' }}</span>
                                        @if ($activity->causer?->email)
                                            <span class="mt-0.5 block text-xs text-slate-400 dark:text-slate-500" dir="ltr">{{ $activity->causer->email }}</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($isLogin)
                                            <span class="badge bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                                </svg>
                                                {{ $activity->description }}
                                            </span>
                                        @else
                                            <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ $activity->description }}</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        @if ($isLogin)
                                            <span class="font-medium text-slate-500 dark:text-slate-400">—</span>
                                        @else
                                            <span class="font-medium">{{ $modelLabels[$type] ?? $type }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-400 dark:text-slate-500">#{{ $activity->subject_id }}</span>
                                        @endif
                                    </td>
                                    <td class="table-td">
                                        <div class="max-w-md space-y-1">
                                            @if ($isLogin)
                                                @if ($ip)
                                                    <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 text-xs leading-5" dir="ltr">
                                                        <span class="font-semibold text-emerald-700 dark:text-emerald-300">IP:</span>
                                                        <span class="text-emerald-800 dark:text-emerald-200">{{ $ip }}</span>
                                                    </div>
                                                @endif
                                                @if ($browser)
                                                    <div class="rounded-lg bg-slate-50 dark:bg-slate-800 px-2 py-1 text-xs leading-5" dir="auto">
                                                        <span class="font-semibold text-slate-600 dark:text-slate-300">مرورگر:</span>
                                                        <span class="text-slate-700 dark:text-slate-200">{{ $browser }}</span>
                                                    </div>
                                                @endif
                                            @endif
                                            @forelse ($attributes as $key => $value)
                                                <div class="rounded-lg bg-slate-50 dark:bg-slate-800 px-2 py-1 text-xs leading-5" dir="auto">
                                                    <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $key }}:</span>
                                                    <span class="text-slate-700 dark:text-slate-200">{{ is_scalar($value) ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE) }}</span>
                                                </div>
                                            @empty
                                                @unless ($isLogin && ($ip || $browser))
                                                    <span class="text-xs text-slate-400 dark:text-slate-500">تغییری ثبت نشده است.</span>
                                                @endunless
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 px-4 py-3">
                    {{ $activities->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
