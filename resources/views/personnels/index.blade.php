@extends('layouts.app')

@section('title', 'پرسنل')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">مدیریت پرسنل</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">جست‌وجو، فیلتر و مدیریت اطلاعات پرسنل راه‌آهن.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('personnels.export', $filters) }}" class="btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    خروجی اکسل / CSV
                </a>
                <a href="{{ route('personnels.export-all-pdf', $filters) }}" class="btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M3.75 17.25v1.5A2.25 2.25 0 016 21h12a2.25 2.25 0 002.25-2.25v-1.5" />
                    </svg>
                    PDF همه پرسنل
                </a>
                @can('create_personnels')
                    <a href="{{ route('personnels.create') }}" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        افزودن پرسنل
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filters --}}
        <div class="card p-4">
            @php
                $defaultDirection = $filters['sort'] === 'name' ? 'asc' : 'desc';
                $hasFilters = $filters['search'] !== ''
                    || $filters['role_id'] !== null
                    || $filters['status'] !== null
                    || $filters['sort'] !== 'name'
                    || $filters['direction'] !== $defaultDirection;
            @endphp

            <form method="GET" action="{{ route('personnels.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="sm:col-span-2 lg:col-span-2">
                    <label for="search" class="form-label">جست‌وجو</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400 dark:text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                        <input id="search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="نام، نام خانوادگی یا کد ملی..."
                            class="form-input pr-11">
                    </div>
                </div>

                <div>
                    <label for="role_id" class="form-label">نقش</label>
                    <select id="role_id" name="role_id" class="form-select">
                        <option value="">همه نقش‌ها</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected($filters['role_id'] === $role->id)>{{ $role->name_fa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="form-label">وضعیت</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">همه</option>
                        <option value="active" @selected($filters['status'] === 'active')>فعال</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>غیرفعال</option>
                    </select>
                </div>

                <div>
                    <label for="sort" class="form-label">مرتبسازی بر اساس</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="name" @selected($filters['sort'] === 'name')>نام</option>
                        <option value="score" @selected($filters['sort'] === 'score')>امتیاز نهایی</option>
                        <option value="entry_year" @selected($filters['sort'] === 'entry_year')>سال ورود</option>
                        <option value="decree_year" @selected($filters['sort'] === 'decree_year')>سال صدور حکم</option>
                    </select>
                </div>

                <div>
                    <label for="direction" class="form-label">ترتیب</label>
                    <div class="flex gap-2">
                        <select id="direction" name="direction" class="form-select">
                            <option value="desc" @selected($filters['direction'] === 'desc')>نزولی</option>
                            <option value="asc" @selected($filters['direction'] === 'asc')>صعودی</option>
                        </select>
                        <button type="submit" class="btn-primary shrink-0" aria-label="اعمال فیلتر">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if ($hasFilters)
                    <div class="sm:col-span-2 lg:col-span-6">
                        <a href="{{ route('personnels.index') }}" class="text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-rose-600">پاک کردن فیلترها</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست پرسنل</h3>
                <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ fa_num(number_format($personnels->total())) }} نفر</span>
            </div>

            @if ($personnels->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-sm font-bold text-slate-800 dark:text-slate-100">پرسنلی یافت نشد</h4>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">فیلترها را تغییر دهید یا پرسنل جدیدی اضافه کنید.</p>
                    <a href="{{ route('personnels.create') }}" class="btn-primary mt-5">افزودن پرسنل</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                            <tr>
                                <th class="table-th">پرسنل</th>
                                <th class="table-th">نقش</th>
                                <th class="table-th">سال ورود</th>
                                <th class="table-th">سال حکم</th>
                                <th class="table-th">امتیاز نهایی</th>
                                <th class="table-th">کد ملی</th>
                                <th class="table-th">وضعیت</th>
                                <th class="table-th">ارزیابی‌ها</th>
                                <th class="table-th text-left">عملیات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($personnels as $personnel)
                                <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                    <td class="table-td">
                                        <div class="flex items-center gap-3">
                                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-sky-400 text-sm font-bold text-white">
                                                {{ mb_substr($personnel->first_name, 0, 1) }}
                                            </span>
                                            <div>
                                                <a href="{{ route('personnels.show', $personnel) }}" class="font-semibold text-slate-800 dark:text-slate-100 transition hover:text-indigo-600">{{ $personnel->full_name }}</a>
                                                <p class="text-xs text-slate-400 dark:text-slate-500">#{{ fa_num($personnel->id) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-td">{{ $personnel->role?->name_fa }}</td>
                                    <td class="table-td">{{ fa_num($personnel->entry_year) }}</td>
                                    <td class="table-td">{{ $personnel->decree_year ? fa_num($personnel->decree_year) : '—' }}</td>
                                    <td class="table-td">
                                        <span class="font-semibold text-indigo-600 dark:text-indigo-300">{{ fa_num(number_format((float) $personnel->computed_score, 2)) }}</span>
                                    </td>
                                    <td class="table-td" dir="ltr">{{ fa_num($personnel->national_code) }}</td>
                                    <td class="table-td">
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
                                    </td>
                                    <td class="table-td">
                                        <a href="{{ route('evaluations.index', ['personnel_id' => $personnel->id]) }}" class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60">
                                            {{ fa_num(number_format($personnel->evaluations_count)) }} مورد
                                        </a>
                                    </td>
                                    <td class="table-td">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('personnels.show', $personnel) }}" class="flex size-9 items-center justify-center rounded-lg border border-indigo-100 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60" title="مشاهده پروفایل">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                            @can('create_evaluations')
                                                <a href="{{ route('evaluations.create', ['personnel_id' => $personnel->id]) }}" class="flex size-9 items-center justify-center rounded-lg border border-indigo-100 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60" title="ثبت ارزیابی">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('edit_personnels')
                                                <a href="{{ route('personnels.edit', $personnel) }}" class="flex size-9 items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700" title="ویرایش">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </a>
                                            @endcan
                                            @can('delete_personnels')
                                                <form method="POST" action="{{ route('personnels.destroy', $personnel) }}" onsubmit="return confirm('آیا از حذف {{ $personnel->full_name }} مطمئن هستید؟ تمام ارزیابی‌های این پرسنل نیز حذف می‌شوند.');">
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

                @if ($personnels->hasPages())
                    <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-4">
                        {{ $personnels->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
