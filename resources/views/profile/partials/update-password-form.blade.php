<section>
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">تغییر رمز عبور</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">برای حفظ امنیت، از یک رمز عبور طولانی و تصادفی استفاده کنید.</p>
    </header>

    <div class="mt-6 space-y-5">
        <div>
            <label for="update_password_current_password" class="form-label">رمز عبور فعلی</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="form-input">
            @error('current_password', 'updatePassword')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password" class="form-label">رمز عبور جدید</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="form-input">
            @error('password', 'updatePassword')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="form-label">تکرار رمز عبور جدید</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="form-input">
            @error('password_confirmation', 'updatePassword')<p class="form-error">{{ $message }}</p>@enderror
        </div>
    </div>
</section>
