@php
    $period = $period ?? null;
    $isEdit = (bool) $period;
@endphp

<form method="POST" action="{{ $isEdit ? route('periods.update', $period) : route('periods.store') }}" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="card p-6">
        <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
            <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </span>
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">اطلاعات دوره ارزیابی</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500">عنوان و بازه زمانی دوره را مشخص کنید.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="title" class="form-label">عنوان دوره <span class="text-rose-500">*</span></label>
                <input id="title" name="title" type="text" value="{{ old('title', $period?->title) }}" required
                    class="form-input @error('title') border-rose-400 @enderror" placeholder="مثلاً دوره اول - 1404">
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="start_date_picker" class="form-label">تاریخ شروع <span class="text-rose-500">*</span></label>
                <input id="start_date" name="start_date" type="hidden" required
                    value="{{ old('start_date', $period?->start_date?->format('Y-m-d')) }}">
                <input id="start_date_picker" type="text" readonly autocomplete="off" placeholder="۱۴۰۴/۰۱/۰۱"
                    class="form-input @error('start_date') border-rose-400 @enderror" dir="rtl">
                <p id="start_date_shamsi" class="mt-1 text-xs text-slate-400 dark:text-slate-500"></p>
                @error('start_date')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="end_date_picker" class="form-label">تاریخ پایان <span class="text-rose-500">*</span></label>
                <input id="end_date" name="end_date" type="hidden" required
                    value="{{ old('end_date', $period?->end_date?->format('Y-m-d')) }}">
                <input id="end_date_picker" type="text" readonly autocomplete="off" placeholder="۱۴۰۴/۰۱/۰۱"
                    class="form-input @error('end_date') border-rose-400 @enderror" dir="rtl">
                <p id="end_date_shamsi" class="mt-1 text-xs text-slate-400 dark:text-slate-500"></p>
                @error('end_date')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label for="is_active" class="flex cursor-pointer items-center justify-between gap-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-3 transition hover:bg-slate-100 dark:hover:bg-slate-700">
                    <span>
                        <span class="block text-sm font-medium text-slate-700 dark:text-slate-200">دوره فعال</span>
                        <span class="block text-xs text-slate-400 dark:text-slate-500">فقط دوره‌های فعال در فرم ثبت ارزیابی نمایش داده می‌شوند.</span>
                    </span>
                    <input type="hidden" name="is_active" value="0">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $period?->is_active ?? true))
                        class="size-5 rounded border-slate-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-300 focus:ring-indigo-500">
                </label>
            </div>
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('periods.index') }}" class="btn-secondary">انصراف</a>
        <button type="submit" class="btn-primary">{{ $isEdit ? 'به‌روزرسانی دوره' : 'ثبت دوره' }}</button>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.initShamsiDatepicker('start_date', 'start_date_picker', 'start_date_shamsi');
            window.initShamsiDatepicker('end_date', 'end_date_picker', 'end_date_shamsi');
        });
    </script>
@endpush
