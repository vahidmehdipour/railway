@extends('layouts.app')

@section('title', 'ویرایش نقش پرسنل')

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">ویرایش نقش پرسنل</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">نام نقش «{{ $role->name_fa }}» را تغییر دهید.</p>
            </div>
            <a href="{{ route('settings.roles.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 transition hover:bg-slate-50 dark:hover:bg-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                بازگشت
            </a>
        </div>

        <form method="POST" action="{{ route('settings.roles.update', $role) }}" class="card space-y-5 p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name_fa" class="form-label">نام فارسی نقش</label>
                <input
                    id="name_fa"
                    name="name_fa"
                    type="text"
                    required
                    value="{{ old('name_fa', $role->name_fa) }}"
                    placeholder="مثلاً رئیس قطار"
                    class="form-input @error('name_fa') border-rose-400 @enderror"
                >
                @error('name_fa')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="name_en" class="form-label">نام انگلیسی نقش</label>
                <input
                    id="name_en"
                    name="name_en"
                    type="text"
                    required
                    dir="ltr"
                    value="{{ old('name_en', $role->name_en) }}"
                    placeholder="مثلاً Train Chief"
                    class="form-input @error('name_en') border-rose-400 @enderror"
                >
                @error('name_en')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end border-t border-slate-100 dark:border-slate-700 pt-5">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection