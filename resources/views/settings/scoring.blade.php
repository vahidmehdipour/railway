@extends('layouts.app')

@section('title', 'تنظیمات امتیازدهی')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 dark:text-indigo-300 hover:text-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                </svg>
                بازگشت به تنظیمات
            </a>
            <h2 class="mt-3 text-xl font-bold text-slate-900 dark:text-white">تنظیمات امتیازدهی</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">حداکثر نمرات و ضرایب کسر امتیاز را در این بخش تنظیم کنید.</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="card overflow-hidden">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700 px-5 py-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">مقادیر محاسبه امتیاز</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">این مقادیر در محاسبه میانگین و امتیاز نهایی پرسنل استفاده میشوند.</p>
                </div>
                <span class="badge bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">۶ مقدار</span>
            </div>

            <div class="space-y-6 p-5">
                <div>
                    <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">حداکثر نمرات</h4>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div>
                            <label for="max_theory_score" class="form-label">حداکثر نمره تئوری</label>
                            <input id="max_theory_score" name="max_theory_score" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('max_theory_score', $settings['max_theory_score'])) }}"
                                class="form-input @error('max_theory_score') border-rose-400 @enderror" dir="rtl">
                            @error('max_theory_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="max_practical_score" class="form-label">حداکثر نمره عملی</label>
                            <input id="max_practical_score" name="max_practical_score" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('max_practical_score', $settings['max_practical_score'])) }}"
                                class="form-input @error('max_practical_score') border-rose-400 @enderror" dir="rtl">
                            @error('max_practical_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="max_ethics_score" class="form-label">حداکثر نمره اخلاق</label>
                            <input id="max_ethics_score" name="max_ethics_score" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('max_ethics_score', $settings['max_ethics_score'])) }}"
                                class="form-input @error('max_ethics_score') border-rose-400 @enderror" dir="rtl">
                            @error('max_ethics_score')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-6">
                    <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">ضرایب کسر امتیاز</h4>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div>
                            <label for="accident_coefficient" class="form-label">ضریب هر سانحه</label>
                            <input id="accident_coefficient" name="accident_coefficient" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('accident_coefficient', $settings['accident_coefficient'])) }}"
                                class="form-input @error('accident_coefficient') border-rose-400 @enderror" dir="rtl">
                            @error('accident_coefficient')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="near_miss_coefficient" class="form-label">ضریب هر شبه سانحه</label>
                            <input id="near_miss_coefficient" name="near_miss_coefficient" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('near_miss_coefficient', $settings['near_miss_coefficient'])) }}"
                                class="form-input @error('near_miss_coefficient') border-rose-400 @enderror" dir="rtl">
                            @error('near_miss_coefficient')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="warning_coefficient" class="form-label">ضریب هر اخطار</label>
                            <input id="warning_coefficient" name="warning_coefficient" type="text" inputmode="decimal" required
                                value="{{ fa_input(old('warning_coefficient', $settings['warning_coefficient'])) }}"
                                class="form-input @error('warning_coefficient') border-rose-400 @enderror" dir="rtl">
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
    </div>
@endsection
