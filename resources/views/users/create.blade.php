@extends('layouts.app')

@section('title', 'افزودن کاربر')

@section('content')
    @php
        $selectedPermissions = old('permissions', []);
    @endphp

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-700" aria-label="بازگشت">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">افزودن کاربر</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">اطلاعات کاربر، نقش و مجوزهای دسترسی او را مشخص کنید.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            <section class="card p-5 sm:p-6">
                <h3 class="mb-5 text-sm font-bold text-slate-900 dark:text-white">اطلاعات کاربری</h3>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="form-label">نام</label>
                        <input id="name" name="name" type="text" autocomplete="name" required
                            value="{{ old('name') }}"
                            class="form-input @error('name') border-rose-400 @enderror">
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="form-label">ایمیل</label>
                        <input id="email" name="email" type="email" autocomplete="email" dir="ltr" required
                            value="{{ old('email') }}"
                            class="form-input @error('email') border-rose-400 @enderror">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="form-label">رمز عبور</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" dir="ltr" required
                            class="form-input @error('password') border-rose-400 @enderror">
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">تأیید رمز عبور</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" dir="ltr" required
                            class="form-input">
                    </div>

                    <label for="is_active" class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 sm:col-span-2">
                        <span>
                            <span class="block text-sm font-medium text-slate-700 dark:text-slate-200">حساب فعال</span>
                            <span class="block text-xs text-slate-400 dark:text-slate-500">کاربران غیرفعال نمیتوانند وارد سیستم شوند.</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))
                                class="size-5 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500">
                        </span>
                    </label>
                </div>
            </section>

            <section class="card overflow-hidden">
                <div class="border-b border-slate-100 dark:border-slate-700 px-5 py-4 sm:px-6">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">نقش و مجوزها</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">نقش، مجموعه مجوزهای پیشفرض را تعیین میکند و مجوزهای اختصاصی میتوانند موارد دلخواه را اضافه کنند.</p>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="max-w-md">
                        <label for="role" class="form-label">نقش کاربر</label>
                        <select id="role" name="role" class="form-select @error('role') border-rose-400 @enderror">
                            <option value="">بدون نقش (فقط مجوزهای اختصاصی)</option>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected(old('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 p-5 sm:p-6">
                    <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">مجوزهای اختصاصی</h4>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">مجوزهای مورد نظر را از دستهبندیهای زیر انتخاب کنید.</p>
                        </div>
                        <span class="badge self-start bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">{{ $permissions->count() }} مجوز</span>
                    </div>

                    @error('permissions')<p class="form-error mb-3">{{ $message }}</p>@enderror
                    @error('permissions.*')<p class="form-error mb-3">{{ $message }}</p>@enderror

                    <div class="space-y-3">
                        @foreach ($permissionGroups as $group)
                            <details class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40" open>
                                <summary class="cursor-pointer bg-slate-50/70 dark:bg-slate-800/70 px-4 py-3 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ $group['label'] }}
                                    <span class="mr-2 text-xs font-normal text-slate-400 dark:text-slate-500">{{ count($group['permissions']) }} مجوز</span>
                                </summary>
                                <div class="grid grid-cols-1 gap-2 p-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($group['permissions'] as $permission)
                                        <label class="flex cursor-pointer items-start gap-2.5 rounded-lg border border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/60 px-3 py-2.5 transition hover:border-indigo-200 dark:hover:border-indigo-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                @checked(in_array($permission->name, $selectedPermissions, true))
                                                class="mt-0.5 size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-800">
                                            <span class="min-w-0">
                                                <span class="block text-sm font-medium text-slate-700 dark:text-slate-200">{{ \App\Support\Permissions::labelFor($permission->name) }}</span>
                                                <span class="mt-0.5 block text-[11px] text-slate-400 dark:text-slate-500" dir="ltr">{{ $permission->name }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">انصراف</a>
                <button type="submit" class="btn-primary">ایجاد کاربر</button>
            </div>
        </form>
    </div>
@endsection
