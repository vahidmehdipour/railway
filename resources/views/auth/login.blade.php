<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">ورود به حساب کاربری</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">برای ادامه، اطلاعات حساب خود را وارد کنید.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('captcha_required'))
        <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-6 text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
            به دلیل تلاشهای ناموفق متعدد، لطفاً کد امنیتی را نیز وارد کنید.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="form-label">ایمیل</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                dir="ltr"
                class="form-input text-left"
                placeholder="you@example.com"
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label">رمز عبور</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="form-input"
                placeholder="••••••••"
            >
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        @if (session('captcha_required'))
            <div>
                <label for="captcha" class="form-label">کد امنیتی</label>
                <div class="mb-2 flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3 dark:border-indigo-900 dark:bg-indigo-950/40">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">حاصل جمع زیر را وارد کنید:</span>
                    <span class="text-xl font-extrabold tracking-tight text-indigo-700 dark:text-indigo-300" dir="ltr">{{ session('captcha_a') }} + {{ session('captcha_b') }} = ?</span>
                </div>
                <input
                    id="captcha"
                    type="text"
                    name="captcha"
                    inputmode="numeric"
                    autocomplete="off"
                    required
                    class="form-input text-center"
                    placeholder="عدد حاصل را وارد کنید"
                >
                @error('captcha')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800">
                <span class="text-sm text-slate-600 dark:text-slate-300">مرا به خاطر بسپار</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                    رمز عبور را فراموش کرده اید؟
                </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
            </svg>
            ورود
        </button>
    </form>
</x-guest-layout>
