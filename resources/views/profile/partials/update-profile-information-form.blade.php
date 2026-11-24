<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">اطلاعات پروفایل</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">نام و ایمیل حساب کاربری خود را به روزرسانی کنید.</p>
    </header>

    <div class="mt-6 space-y-5">
        <div>
            <label for="name" class="form-label">نام</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="form-input">
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="form-label">ایمیل</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="form-input" dir="ltr">
            @error('email')<p class="form-error">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                        ایمیل شما تأیید نشده است.
                        <button type="submit" form="send-verification" class="text-sm font-medium text-indigo-600 dark:text-indigo-300 underline hover:text-indigo-500">
                            برای ارسال مجدد ایمیل تأیید کلیک کنید.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-300">لینک تأیید جدید به ایمیل شما ارسال شد.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
