@extends('layouts.app')

@section('title', 'دوره‌های ارزیابی')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">دوره‌های ارزیابی</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">مدیریت بازه‌های زمانی ارزیابی پرسنل.</p>
            </div>

            @can('create_periods')
                <a href="{{ route('periods.create') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    افزودن دوره
                </a>
            @endcan
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست دوره‌ها</h3>
                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ number_format($periods->count()) }} دوره</span>
            </div>

            @if ($periods->isEmpty())
                <div class="px-6 py-16 text-center">
                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">دوره‌ای تعریف نشده است</h4>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">برای ثبت ارزیابی ابتدا یک دوره ایجاد کنید.</p>
                    <a href="{{ route('periods.create') }}" class="btn-primary mt-5">افزودن دوره</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                            <tr>
                                <th class="table-th">عنوان دوره</th>
                                <th class="table-th">تاریخ شروع</th>
                                <th class="table-th">تاریخ پایان</th>
                                <th class="table-th">وضعیت</th>
                                <th class="table-th">ارزیابی‌ها</th>
                                <th class="table-th text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($periods as $period)
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                    <td class="table-td font-semibold text-slate-800 dark:text-slate-100">{{ $period->title }}</td>
                                    <td class="table-td" dir="ltr">{{ jdate($period->start_date) }}</td>
                                    <td class="table-td" dir="ltr">{{ jdate($period->end_date) }}</td>
                                    <td class="table-td">
                                        @if ($period->is_active)
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
                                    </td>
                                    <td class="table-td">
                                        <a href="{{ route('evaluations.index', ['period_id' => $period->id]) }}" class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60">
                                            {{ number_format($period->evaluations_count) }} مورد
                                        </a>
                                    </td>
                                    <td class="table-td">
                                        <div class="flex items-center justify-end gap-2">
                                            @can('edit_periods')
                                                <a href="{{ route('periods.edit', $period) }}" class="flex size-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700" title="ویرایش">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('delete_periods')
                                                <form method="POST" action="{{ route('periods.destroy', $period) }}" onsubmit="return confirm('آیا از حذف دوره {{ $period->title }} مطمئن هستید؟');">
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
            @endif
        </div>
    </div>
@endsection
