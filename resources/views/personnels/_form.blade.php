@php
    $personnel = $personnel ?? null;
    $isEdit = (bool) $personnel;
@endphp

<form method="POST" action="{{ $isEdit ? route('personnels.update', $personnel) : route('personnels.store') }}" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="card p-6">
        <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
            <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </span>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">اطلاعات پرسنل</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500">فیلدهای ستاره‌دار الزامی هستند.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label for="first_name" class="form-label">نام <span class="text-rose-500">*</span></label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $personnel?->first_name) }}" required
                    class="form-input @error('first_name') border-rose-400 @enderror" placeholder="مثلاً علی">
                @error('first_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="last_name" class="form-label">نام خانوادگی <span class="text-rose-500">*</span></label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $personnel?->last_name) }}" required
                    class="form-input @error('last_name') border-rose-400 @enderror" placeholder="مثلاً رضایی">
                @error('last_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="role_id" class="form-label">نقش <span class="text-rose-500">*</span></label>
                <select id="role_id" name="role_id" required class="form-select @error('role_id') border-rose-400 @enderror">
                    <option value="" disabled @selected(! old('role_id', $personnel?->role_id))>انتخاب نقش...</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((int) old('role_id', $personnel?->role_id) === $role->id)>
                            {{ $role->name_fa }} ({{ $role->name_en }})
                        </option>
                    @endforeach
                </select>
                @error('role_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="entry_year" class="form-label">سال ورود <span class="text-rose-500">*</span></label>
                <input id="entry_year" name="entry_year" type="number" min="1300" max="2100" value="{{ old('entry_year', $personnel?->entry_year) }}" required
                    class="form-input @error('entry_year') border-rose-400 @enderror" placeholder="مثلاً 1398" dir="ltr">
                @error('entry_year')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="decree_year" class="form-label">سال صدور حکم</label>
                <input id="decree_year" name="decree_year" type="number" min="1300" max="2100" value="{{ old('decree_year', $personnel?->decree_year) }}"
                    class="form-input @error('decree_year') border-rose-400 @enderror" placeholder="مثلاً 1400" dir="ltr">
                @error('decree_year')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="national_code" class="form-label">کد ملی</label>
                <input id="national_code" name="national_code" type="text" inputmode="numeric" maxlength="10" value="{{ old('national_code', $personnel?->national_code) }}"
                    class="form-input @error('national_code') border-rose-400 @enderror" placeholder="۱۰ رقم" dir="ltr">
                @error('national_code')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-end">
                <label for="is_active" class="flex w-full cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 transition hover:bg-slate-100 dark:hover:bg-slate-700">
                    <span>
                        <span class="block text-sm font-medium text-slate-700 dark:text-slate-200">وضعیت پرسنل</span>
                        <span class="block text-xs text-slate-400 dark:text-slate-500">در صورت غیرفعال بودن، در فرم ارزیابی نمایش داده نمی‌شود.</span>
                    </span>
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $personnel?->is_active ?? true))
                        class="size-5 rounded border-slate-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-300 focus:ring-indigo-500">
                </label>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('personnels.index') }}" class="btn-secondary">انصراف</a>
        <button type="submit" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            {{ $isEdit ? 'به‌روزرسانی پرسنل' : 'ثبت پرسنل' }}
        </button>
    </div>
</form>
