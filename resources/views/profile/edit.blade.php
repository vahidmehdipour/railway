@extends('layouts.app')

@section('title', 'پروفایل من')

@section('content')
    @if ($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 6000)" x-show="show" x-transition
            class="mx-auto mb-5 max-w-3xl flex items-start gap-3 rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-800 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-rose-500">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <p class="font-medium">ذخیره نشد؛ لطفاً خطاهای فرم را بررسی کنید.</p>
        </div>
    @endif

    <div class="mx-auto max-w-3xl">
        <div class="card p-6 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="my-8 border-t border-slate-200 dark:border-slate-700"></div>

            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection
