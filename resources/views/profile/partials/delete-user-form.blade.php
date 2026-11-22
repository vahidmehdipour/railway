<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">حذف حساب کاربری</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            پس از حذف حساب، تمام اطلاعات مرتبط با آن به‌صورت دائمی حذف می‌شود. پیش از حذف، در صورت نیاز از اطلاعات خود نسخه پشتیبان تهیه کنید.
        </p>
    </header>

    <button
        type="button"
        class="btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >حذف حساب کاربری</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900 dark:text-white">آیا از حذف حساب کاربری مطمئن هستید؟</h2>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                پس از حذف حساب، تمام اطلاعات مرتبط با آن به‌صورت دائمی حذف می‌شود. برای تأیید، رمز عبور خود را وارد کنید.
            </p>

            <div class="mt-6">
                <label for="delete_password" class="sr-only">رمز عبور</label>
                <input id="delete_password" name="password" type="password" class="form-input" placeholder="رمز عبور">
                @error('password', 'userDeletion')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">انصراف</button>
                <button type="submit" class="btn-danger">حذف حساب</button>
            </div>
        </form>
    </x-modal>
</section>
