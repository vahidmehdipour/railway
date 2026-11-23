@extends('layouts.app')

@section('title', 'تنظیمات سیستم')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">تنظیمات سیستم</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                حداکثر نمرات، ضرایب کسر امتیاز و ایستگاه‌های قابل انتخاب در فرم ارزیابی را از این بخش مدیریت کنید.
            </p>
        </div>

        {{-- Scoring settings --}}
        <form method="POST" action="{{ route('settings.update') }}" class="card overflow-hidden">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">تنظیمات امتیازدهی</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">این مقادیر در محاسبه میانگین و امتیاز نهایی پرسنل استفاده می‌شوند.</p>
                </div>
                <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">۶ مقدار</span>
            </div>

            <div class="space-y-6 p-5">
                <div>
                    <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">حداکثر نمرات</h4>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div>
                            <label for="max_theory_score" class="form-label">حداکثر نمره تئوری</label>
                            <input id="max_theory_score" name="max_theory_score" type="number" min="0" step="0.01" required
                                value="{{ old('max_theory_score', $settings['max_theory_score']) }}"
                                class="form-input @error('max_theory_score') border-rose-400 @enderror" dir="ltr">
                            @error('max_theory_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="max_practical_score" class="form-label">حداکثر نمره عملی</label>
                            <input id="max_practical_score" name="max_practical_score" type="number" min="0" step="0.01" required
                                value="{{ old('max_practical_score', $settings['max_practical_score']) }}"
                                class="form-input @error('max_practical_score') border-rose-400 @enderror" dir="ltr">
                            @error('max_practical_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="max_ethics_score" class="form-label">حداکثر نمره اخلاق</label>
                            <input id="max_ethics_score" name="max_ethics_score" type="number" min="0" step="0.01" required
                                value="{{ old('max_ethics_score', $settings['max_ethics_score']) }}"
                                class="form-input @error('max_ethics_score') border-rose-400 @enderror" dir="ltr">
                            @error('max_ethics_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                    <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">ضرایب کسر امتیاز</h4>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div>
                            <label for="accident_coefficient" class="form-label">ضریب هر سانحه</label>
                            <input id="accident_coefficient" name="accident_coefficient" type="number" min="0" step="0.01" required
                                value="{{ old('accident_coefficient', $settings['accident_coefficient']) }}"
                                class="form-input @error('accident_coefficient') border-rose-400 @enderror" dir="ltr">
                            @error('accident_coefficient')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="near_miss_coefficient" class="form-label">ضریب هر شبه سانحه</label>
                            <input id="near_miss_coefficient" name="near_miss_coefficient" type="number" min="0" step="0.01" required
                                value="{{ old('near_miss_coefficient', $settings['near_miss_coefficient']) }}"
                                class="form-input @error('near_miss_coefficient') border-rose-400 @enderror" dir="ltr">
                            @error('near_miss_coefficient')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="warning_coefficient" class="form-label">ضریب هر اخطار</label>
                            <input id="warning_coefficient" name="warning_coefficient" type="number" min="0" step="0.01" required
                                value="{{ old('warning_coefficient', $settings['warning_coefficient']) }}"
                                class="form-input @error('warning_coefficient') border-rose-400 @enderror" dir="ltr">
                            @error('warning_coefficient')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 dark:border-slate-700 pt-5">
                    <button type="submit" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        ذخیره تنظیمات
                    </button>
                </div>
            </div>
        </form>

        {{-- Stations --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <form method="POST" action="{{ route('settings.stations.store') }}" class="card h-fit p-5 xl:col-span-1">
                @csrf

                <h3 class="text-sm font-bold text-slate-900 dark:text-white">افزودن ایستگاه</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">ایستگاه‌ها به صورت اختیاری در فرم ارزیابی انتخاب می‌شوند.</p>

                <div class="mt-4">
                    <label for="name" class="form-label">نام ایستگاه</label>
                    <input id="name" name="name" type="text" required value="{{ old('name') }}"
                        placeholder="مثلاً ایستگاه تهران"
                        class="form-input @error('name') border-rose-400 @enderror">
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-primary mt-4 w-full justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    افزودن ایستگاه
                </button>
            </form>

            <div class="card overflow-hidden xl:col-span-2">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست ایستگاه‌ها</h3>
                    <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ number_format($stations->count()) }} ایستگاه</span>
                </div>

                @if ($stations->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">ایستگاهی ثبت نشده است</h4>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">با استفاده از فرم کناری اولین ایستگاه را اضافه کنید.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                                <tr>
                                    <th class="table-th">نام ایستگاه</th>
                                    <th class="table-th">ارزیابی‌ها</th>
                                    <th class="table-th text-left">عملیات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($stations as $station)
                                    <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                        <td class="table-td font-semibold text-slate-800 dark:text-slate-100">{{ $station->name }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ number_format($station->evaluations_count) }} مورد</span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('settings.stations.edit', $station) }}" class="flex size-9 items-center justify-center rounded-lg border border-indigo-100 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60" title="ویرایش">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </a>
                                                @role('super-admin')
                                                    <form method="POST" action="{{ route('settings.stations.destroy', $station) }}"
                                                        onsubmit="return confirm('آیا از حذف ایستگاه {{ $station->name }} مطمئن هستید؟ ارزیابی‌های مرتبط بدون ایستگاه خواهند شد.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex size-9 items-center justify-center rounded-lg border border-rose-100 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300 transition hover:bg-rose-100 dark:hover:bg-rose-900/60" title="حذف">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endrole
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

        {{-- Personnel roles --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <form method="POST" action="{{ route('settings.roles.store') }}" class="card h-fit p-5 xl:col-span-1">
                @csrf

                <h3 class="text-sm font-bold text-slate-900 dark:text-white">افزودن نقش پرسنل</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">نقشها در فرم تعریف پرسنل قابل انتخاب خواهند بود.</p>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="role_name_fa" class="form-label">نام فارسی نقش</label>
                        <input id="role_name_fa" name="name_fa" type="text" required value="{{ old('name_fa') }}"
                            placeholder="مثلاً رئیس قطار"
                            class="form-input @error('name_fa') border-rose-400 @enderror">
                        @error('name_fa')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="role_name_en" class="form-label">نام انگلیسی نقش</label>
                        <input id="role_name_en" name="name_en" type="text" required value="{{ old('name_en') }}" dir="ltr"
                            placeholder="مثلاً Train Chief"
                            class="form-input @error('name_en') border-rose-400 @enderror">
                        @error('name_en')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="btn-primary mt-4 w-full justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    افزودن نقش
                </button>
            </form>

            <div class="card overflow-hidden xl:col-span-2">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">لیست نقشهای پرسنل</h3>
                    <span class="badge bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ number_format($roles->count()) }} نقش</span>
                </div>

                @if ($roles->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100">نقشی ثبت نشده است</h4>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">با استفاده از فرم کناری اولین نقش را اضافه کنید تا بتوانید پرسنل تعریف نمایید.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead class="bg-slate-50/70 dark:bg-slate-800/70">
                                <tr>
                                    <th class="table-th">نام فارسی</th>
                                    <th class="table-th">نام انگلیسی</th>
                                    <th class="table-th">پرسنل</th>
                                    <th class="table-th text-left">عملیات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($roles as $role)
                                    <tr class="transition hover:bg-slate-50/60 dark:hover:bg-slate-800/60">
                                        <td class="table-td font-semibold text-slate-800 dark:text-slate-100">{{ $role->name_fa }}</td>
                                        <td class="table-td" dir="ltr">{{ $role->name_en }}</td>
                                        <td class="table-td">
                                            <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ number_format($role->personnels_count) }} نفر</span>
                                        </td>
                                        <td class="table-td">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('settings.roles.edit', $role) }}" class="flex size-9 items-center justify-center rounded-lg border border-indigo-100 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 transition hover:bg-indigo-100 dark:hover:bg-indigo-900/60" title="ویرایش">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                    </svg>
                                                </a>
                                                @role('super-admin')
                                                    <form method="POST" action="{{ route('settings.roles.destroy', $role) }}"
                                                        onsubmit="return confirm('آیا از حذف نقش {{ $role->name_fa }} مطمئن هستید؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="flex size-9 items-center justify-center rounded-lg border border-rose-100 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-300 transition hover:bg-rose-100 dark:hover:bg-rose-900/60" title="حذف">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endrole
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
    </div>
@endsection
