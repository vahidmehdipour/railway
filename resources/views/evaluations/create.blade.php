@extends('layouts.app')

@section('title', 'ثبت ارزیابی')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('evaluations.index') }}" class="flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-700" aria-label="بازگشت">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">ثبت ارزیابی جدید</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">نمرات و موارد منفی را وارد کنید؛ امتیاز نهایی به صورت زنده محاسبه می‌شود.</p>
            </div>
        </div>

        @include('evaluations._form', ['evaluation' => null])
    </div>
@endsection
