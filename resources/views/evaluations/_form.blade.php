@php
    $evaluation = $evaluation ?? null;
    $isEdit = (bool) $evaluation;
    $selectedPersonnel = old('personnel_id', $evaluation?->personnel_id ?? $selectedPersonnel ?? null);
    $selectedPeriod = old('period_id', $evaluation?->period_id ?? $selectedPeriod ?? null);
    $selectedStation = old('station_id', $evaluation?->station_id);
    $scoringSettings = $scoringSettings ?? [
        'max_theory_score' => 14,
        'max_practical_score' => 4,
        'max_ethics_score' => 2,
        'accident_coefficient' => 2,
        'near_miss_coefficient' => 1,
        'warning_coefficient' => 0.5,
    ];
    $maxTotal = $scoringSettings['max_theory_score'] + $scoringSettings['max_practical_score'] + $scoringSettings['max_ethics_score'];
@endphp

<form method="POST" action="{{ $isEdit ? route('evaluations.update', $evaluation) : route('evaluations.store') }}" class="grid grid-cols-1 gap-6 xl:grid-cols-3">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-6 xl:col-span-2">
        {{-- Personnel, period, station and date --}}
        <div class="card p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
                <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">اطلاعات ارزیابی</h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">می‌توانید چند ارزیابی برای یک پرسنل در یک دوره ثبت کنید.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="personnel_id" class="form-label">پرسنل <span class="text-rose-500">*</span></label>
                    <select id="personnel_id" name="personnel_id" required class="form-select @error('personnel_id') border-rose-400 @enderror">
                        <option value="" disabled @selected(! $selectedPersonnel)>انتخاب پرسنل...</option>
                        @foreach ($personnels as $personnel)
                            <option value="{{ $personnel->id }}" @selected((int) $selectedPersonnel === $personnel->id)>
                                {{ $personnel->full_name }} — {{ $personnel->role?->name_fa }}@unless ($personnel->is_active) (غیرفعال)@endunless
                            </option>
                        @endforeach
                    </select>
                    @error('personnel_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="period_id" class="form-label">دوره ارزیابی <span class="text-rose-500">*</span></label>
                    <select id="period_id" name="period_id" required class="form-select @error('period_id') border-rose-400 @enderror">
                        <option value="" disabled @selected(! $selectedPeriod)>انتخاب دوره...</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((int) $selectedPeriod === $period->id)>{{ $period->title }}</option>
                        @endforeach
                    </select>
                    @error('period_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="station_id" class="form-label">ایستگاه <span class="text-xs text-slate-400 dark:text-slate-500">(اختیاری)</span></label>
                    <select id="station_id" name="station_id" class="form-select @error('station_id') border-rose-400 @enderror">
                        <option value="">بدون ایستگاه</option>
                        @foreach ($stations as $station)
                            <option value="{{ $station->id }}" @selected((int) $selectedStation === $station->id)>{{ $station->name }}</option>
                        @endforeach
                    </select>
                    @error('station_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="evaluation_date_picker" class="form-label">تاریخ ارزیابی <span class="text-rose-500">*</span></label>
                    <input id="evaluation_date" name="evaluation_date" type="hidden" required
                        value="{{ old('evaluation_date', $evaluation?->evaluation_date?->format('Y-m-d') ?? now()->toDateString()) }}">
                    <input id="evaluation_date_picker" type="text" readonly autocomplete="off" placeholder="۱۴۰۴/۰۱/۰۱"
                        class="form-input @error('evaluation_date') border-rose-400 @enderror" dir="rtl">
                    <p id="evaluation_date_shamsi" class="mt-1 text-xs text-slate-400 dark:text-slate-500"></p>
                    @error('evaluation_date')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="form-label">توضیحات <span class="text-xs text-slate-400 dark:text-slate-500">(اختیاری)</span></label>
                    <textarea id="description" name="description" rows="3"
                        class="form-input @error('description') border-rose-400 @enderror"
                        placeholder="توضیحات، نکات یا دلیل ثبت ارزیابی را وارد کنید...">{{ old('description', $evaluation?->description) }}</textarea>
                    @error('description')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Scores --}}
        <div class="card p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
                <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">نمرات ارزیابی</h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">همه نمرات اختیاری هستند؛ می‌توانید فقط بخشی از نمرات یا صرفاً توضیحات را ثبت کنید.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="theory_score" class="form-label">
                        نمره تئوری
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ $scoringSettings['max_theory_score'] }})</span>
                    </label>
                    <input id="theory_score" name="theory_score" type="number" min="0" max="{{ $scoringSettings['max_theory_score'] }}" step="0.01"
                        value="{{ old('theory_score', $evaluation?->theory_score) }}" placeholder="اختیاری"
                        class="score-input form-input @error('theory_score') border-rose-400 @enderror" dir="ltr">
                    @error('theory_score')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="practical_score" class="form-label">
                        نمره عملی
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ $scoringSettings['max_practical_score'] }})</span>
                    </label>
                    <input id="practical_score" name="practical_score" type="number" min="0" max="{{ $scoringSettings['max_practical_score'] }}" step="0.01"
                        value="{{ old('practical_score', $evaluation?->practical_score) }}" placeholder="اختیاری"
                        class="score-input form-input @error('practical_score') border-rose-400 @enderror" dir="ltr">
                    @error('practical_score')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="ethics_score" class="form-label">
                        نمره اخلاق
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ $scoringSettings['max_ethics_score'] }})</span>
                    </label>
                    <input id="ethics_score" name="ethics_score" type="number" min="0" max="{{ $scoringSettings['max_ethics_score'] }}" step="0.01"
                        value="{{ old('ethics_score', $evaluation?->ethics_score) }}" placeholder="اختیاری"
                        class="score-input form-input @error('ethics_score') border-rose-400 @enderror" dir="ltr">
                    @error('ethics_score')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Penalties --}}
        <div class="card p-6">
            <div class="mb-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
                <span class="flex size-10 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">موارد منفی و تخلفات</h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        هر تصادف {{ $scoringSettings['accident_coefficient'] }} نمره، هر حادثه نزدیک {{ $scoringSettings['near_miss_coefficient'] }} نمره و هر اخطار {{ $scoringSettings['warning_coefficient'] }} نمره کسر می‌کند.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="accident_count" class="form-label">تعداد تصادفات <span class="text-xs text-rose-500">(-{{ $scoringSettings['accident_coefficient'] }})</span></label>
                    <input id="accident_count" name="accident_count" type="number" min="0" step="1" required
                        value="{{ old('accident_count', $evaluation?->accident_count ?? 0) }}"
                        class="score-input form-input @error('accident_count') border-rose-400 @enderror" dir="ltr">
                    @error('accident_count')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="near_miss_count" class="form-label">حوادث نزدیک <span class="text-xs text-rose-500">(-{{ $scoringSettings['near_miss_coefficient'] }})</span></label>
                    <input id="near_miss_count" name="near_miss_count" type="number" min="0" step="1" required
                        value="{{ old('near_miss_count', $evaluation?->near_miss_count ?? 0) }}"
                        class="score-input form-input @error('near_miss_count') border-rose-400 @enderror" dir="ltr">
                    @error('near_miss_count')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="warning_count" class="form-label">تعداد اخطارها <span class="text-xs text-rose-500">(-{{ $scoringSettings['warning_coefficient'] }})</span></label>
                    <input id="warning_count" name="warning_count" type="number" min="0" step="1" required
                        value="{{ old('warning_count', $evaluation?->warning_count ?? 0) }}"
                        class="score-input form-input @error('warning_count') border-rose-400 @enderror" dir="ltr">
                    @error('warning_count')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
            <a href="{{ route('evaluations.index') }}" class="btn-secondary">انصراف</a>
            <button type="submit" class="btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                {{ $isEdit ? 'به‌روزرسانی ارزیابی' : 'ثبت ارزیابی' }}
            </button>
        </div>
    </div>

    {{-- Live total --}}
    <aside class="xl:col-span-1">
        <div class="sticky top-24 space-y-4">
            <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-300">امتیاز این ارزیابی (پیش‌نمایش)</p>
                    <span class="badge bg-white/10 text-slate-200">محاسبه خودکار</span>
                </div>

                <div class="mt-5 flex items-end justify-center gap-2">
                    <span id="total-preview" class="text-6xl font-black tracking-tight text-white">0.00</span>
                    <span class="pb-2 text-sm text-slate-400 dark:text-slate-500">از {{ $maxTotal }}</span>
                </div>

                <div id="total-message" class="mt-4 rounded-xl bg-white/10 px-3 py-2 text-center text-xs font-medium text-slate-200">
                    نمرات را وارد کنید.
                </div>

                <dl class="mt-6 space-y-2.5 border-t border-white/10 pt-5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">مجموع نمرات</dt>
                        <dd id="breakdown-scores" class="font-semibold text-emerald-300">0.00</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر تصادفات</dt>
                        <dd id="breakdown-accidents" class="font-semibold text-rose-300">0.00</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر حوادث نزدیک</dt>
                        <dd id="breakdown-near-miss" class="font-semibold text-rose-300">0.00</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر اخطارها</dt>
                        <dd id="breakdown-warnings" class="font-semibold text-rose-300">0.00</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-5 text-xs leading-6 text-slate-500 dark:text-slate-400">
                <p class="mb-2 font-bold text-slate-700 dark:text-slate-200">فرمول محاسبه امتیاز این ارزیابی</p>
                <p dir="ltr" class="rounded-lg bg-slate-50 dark:bg-slate-800 px-3 py-2 text-center font-mono text-[11px] text-slate-600 dark:text-slate-300">
                    total = theory + practical + ethics − (accidents × {{ $scoringSettings['accident_coefficient'] }}) − (near misses × {{ $scoringSettings['near_miss_coefficient'] }}) − (warnings × {{ $scoringSettings['warning_coefficient'] }})
                </p>
                <p class="mt-3">نمرات خالی در محاسبه صفر در نظر گرفته می‌شوند. امتیاز نهایی پرسنل از میانگین ارزیابی‌ها محاسبه می‌شود.</p>
            </div>
        </div>
    </aside>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.initShamsiDatepicker('evaluation_date', 'evaluation_date_picker', 'evaluation_date_shamsi');

            const settings = @json($scoringSettings);
            const maxTotal = Number(settings.max_theory_score) + Number(settings.max_practical_score) + Number(settings.max_ethics_score);

            const fields = {
                theory: document.getElementById('theory_score'),
                practical: document.getElementById('practical_score'),
                ethics: document.getElementById('ethics_score'),
                accidents: document.getElementById('accident_count'),
                nearMiss: document.getElementById('near_miss_count'),
                warnings: document.getElementById('warning_count'),
            };

            const preview = document.getElementById('total-preview');
            const message = document.getElementById('total-message');
            const scoresOutput = document.getElementById('breakdown-scores');
            const accidentsOutput = document.getElementById('breakdown-accidents');
            const nearMissOutput = document.getElementById('breakdown-near-miss');
            const warningsOutput = document.getElementById('breakdown-warnings');

            const number = (input) => {
                if (!input || input.value.trim() === '') {
                    return 0;
                }

                const value = parseFloat(input.value);

                return Number.isFinite(value) ? value : 0;
            };

            const format = (value) => value.toFixed(2);

            const calculate = () => {
                const scores = number(fields.theory) + number(fields.practical) + number(fields.ethics);
                const accidents = number(fields.accidents) * Number(settings.accident_coefficient);
                const nearMiss = number(fields.nearMiss) * Number(settings.near_miss_coefficient);
                const warnings = number(fields.warnings) * Number(settings.warning_coefficient);
                const total = scores - accidents - nearMiss - warnings;

                const hasAnyScore = [fields.theory, fields.practical, fields.ethics]
                    .some((input) => input && input.value.trim() !== '');

                preview.textContent = format(total);
                scoresOutput.textContent = format(scores);
                accidentsOutput.textContent = '−' + format(accidents);
                nearMissOutput.textContent = '−' + format(nearMiss);
                warningsOutput.textContent = '−' + format(warnings);

                if (!hasAnyScore) {
                    preview.className = 'text-6xl font-black tracking-tight text-slate-300';
                    message.textContent = 'نمره‌ای وارد نشده؛ فقط توضیحات ثبت می‌شود.';
                    message.className = 'mt-4 rounded-xl bg-white/10 px-3 py-2 text-center text-xs font-medium text-slate-200';
                } else if (total >= maxTotal * 0.8) {
                    preview.className = 'text-6xl font-black tracking-tight text-emerald-300';
                    message.textContent = 'عملکرد عالی — در محدوده امتیاز بالا';
                    message.className = 'mt-4 rounded-xl bg-emerald-400/15 px-3 py-2 text-center text-xs font-medium text-emerald-200';
                } else if (total >= maxTotal * 0.5) {
                    preview.className = 'text-6xl font-black tracking-tight text-amber-300';
                    message.textContent = 'عملکرد متوسط — نیازمند بهبود';
                    message.className = 'mt-4 rounded-xl bg-amber-400/15 px-3 py-2 text-center text-xs font-medium text-amber-200';
                } else {
                    preview.className = 'text-6xl font-black tracking-tight text-rose-300';
                    message.textContent = 'عملکرد ضعیف — نیازمند بررسی';
                    message.className = 'mt-4 rounded-xl bg-rose-400/15 px-3 py-2 text-center text-xs font-medium text-rose-200';
                }
            };

            Object.values(fields).forEach((input) => {
                input?.addEventListener('input', calculate);
                input?.addEventListener('change', calculate);
            });

            calculate();
        });
    </script>
@endpush
