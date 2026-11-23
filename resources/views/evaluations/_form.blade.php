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
    $selectedPersonnelModel = $personnels->first(fn ($person) => (int) $selectedPersonnel === $person->id);
    $selectedPersonnelLabel = $selectedPersonnelModel
        ? trim($selectedPersonnelModel->full_name.' — '.$selectedPersonnelModel->role?->name_fa.($selectedPersonnelModel->is_active ? '' : ' (غیرفعال)'))
        : 'انتخاب پرسنل...';
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
                <div x-data="{
                    open: false,
                    search: '',
                    selected: '{{ $selectedPersonnel }}',
                    normalize(value) {
                        return String(value ?? '')
                            .replace(/[\u0660-\u0669]/g, (digit) => String(digit.charCodeAt(0) - 0x0660))
                            .replace(/[\u06F0-\u06F9]/g, (digit) => String(digit.charCodeAt(0) - 0x06F0))
                            .toLowerCase()
                            .trim();
                    },
                    matches(haystack) {
                        const term = this.normalize(this.search);

                        return term === '' || this.normalize(haystack).includes(term);
                    },
                    select(value, option) {
                        const input = document.getElementById('personnel_id');
                        const selected = document.getElementById('personnel_selected_label');
                        const label = option.dataset.label ?? option.textContent.trim();

                        input.value = value;
                        this.selected = value;
                        selected.textContent = label;
                        selected.classList.toggle('text-slate-400', value === '');
                        selected.classList.toggle('dark:text-slate-500', value === '');

                        Array.from(this.$refs.options.querySelectorAll('.personnel-option')).forEach((item) => {
                            item.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300', 'font-semibold');
                        });
                        if (value !== '') {
                            option.classList.add('bg-indigo-50', 'dark:bg-indigo-900/30', 'text-indigo-700', 'dark:text-indigo-300', 'font-semibold');
                        }

                        this.open = false;
                        this.search = '';
                    },
                    get resultText() {
                        if (!this.$refs.options) {
                            return '';
                        }

                        const options = Array.from(this.$refs.options.querySelectorAll('.personnel-option'));
                        const visible = options.filter((option) => this.matches(option.dataset.search ?? '')).length;

                        return (window.toPersianDigits ? window.toPersianDigits(visible) : visible) + ' پرسنل';
                    }
                }" class="relative">
                    <label id="personnel_picker_label" class="form-label">پرسنل <span class="text-rose-500">*</span></label>
                    <input id="personnel_id" name="personnel_id" type="hidden" value="{{ $selectedPersonnel }}">

                    <button type="button" id="personnel_toggle" @click="open = !open; if (open) $nextTick(() => $refs.search.focus());"
                        class="form-select flex w-full items-center justify-between gap-2 text-right @error('personnel_id') border-rose-400 @enderror"
                        aria-haspopup="listbox" :aria-expanded="open" aria-labelledby="personnel_picker_label">
                        <span id="personnel_selected_label" class="truncate {{ $selectedPersonnel ? '' : 'text-slate-400 dark:text-slate-500' }}">{{ $selectedPersonnelLabel }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4 shrink-0 text-slate-400 transition" :class="open && 'rotate-180'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute z-30 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl">
                        <div class="border-b border-slate-100 dark:border-slate-800 p-2">
                            <div class="relative">
                                <input id="personnel_search" x-ref="search" type="search" x-model="search" @keydown.escape.stop="open = false"
                                    placeholder="جستجو در لیست پرسنل (نام، نقش یا کد ملی)..."
                                    class="form-input py-2 pl-9 text-sm" aria-label="جستجو در لیست پرسنل">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                        </div>

                        <ul id="personnel_options" x-ref="options" role="listbox" class="max-h-64 overflow-y-auto p-1">
                            <li>
                                <button type="button" data-search="انتخاب پرسنل..." data-label="انتخاب پرسنل..." x-show="matches($el.dataset.search)" @click="select('', $el)" class="personnel-option w-full rounded-lg px-3 py-2 text-right text-sm text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800" role="option">انتخاب پرسنل...</button>
                            </li>
                            @foreach ($personnels as $personnel)
                                @php
                                    $optionLabel = trim($personnel->full_name.' — '.$personnel->role?->name_fa.($personnel->is_active ? '' : ' (غیرفعال)'));
                                @endphp
                                <li>
                                    <button type="button"
                                        x-show="matches($el.dataset.search)"
                                        data-search="{{ trim($personnel->full_name.' '.$personnel->role?->name_fa.' '.$personnel->national_code) }}"
                                        data-label="{{ $optionLabel }}"
                                        @click="select('{{ $personnel->id }}', $el)"
                                        class="personnel-option w-full rounded-lg px-3 py-2 text-right text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/30 {{ (int) $selectedPersonnel === $personnel->id ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-semibold' : 'text-slate-700 dark:text-slate-200' }}"
                                        role="option" :aria-selected="selected === '{{ $personnel->id }}'">
                                        {{ $optionLabel }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <p id="personnel_search_count" class="border-t border-slate-100 dark:border-slate-800 px-3 py-2 text-xs text-slate-400 dark:text-slate-500" x-text="resultText"></p>
                    </div>
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
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ fa_num($scoringSettings['max_theory_score']) }})</span>
                    </label>
                    <input id="theory_score" name="theory_score" type="text" inputmode="decimal"
                        value="{{ fa_input(old('theory_score', $evaluation?->theory_score)) }}" placeholder="اختیاری"
                        class="score-input form-input @error('theory_score') border-rose-400 @enderror" dir="rtl">
                    @error('theory_score')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="practical_score" class="form-label">
                        نمره عملی
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ fa_num($scoringSettings['max_practical_score']) }})</span>
                    </label>
                    <input id="practical_score" name="practical_score" type="text" inputmode="decimal"
                        value="{{ fa_input(old('practical_score', $evaluation?->practical_score)) }}" placeholder="اختیاری"
                        class="score-input form-input @error('practical_score') border-rose-400 @enderror" dir="rtl">
                    @error('practical_score')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="ethics_score" class="form-label">
                        نمره اخلاق
                        <span class="text-xs font-normal text-slate-400 dark:text-slate-500">(حداکثر {{ fa_num($scoringSettings['max_ethics_score']) }})</span>
                    </label>
                    <input id="ethics_score" name="ethics_score" type="text" inputmode="decimal"
                        value="{{ fa_input(old('ethics_score', $evaluation?->ethics_score)) }}" placeholder="اختیاری"
                        class="score-input form-input @error('ethics_score') border-rose-400 @enderror" dir="rtl">
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
                        هر سانحه {{ $scoringSettings['accident_coefficient'] }} نمره، هر شبه سانحه {{ $scoringSettings['near_miss_coefficient'] }} نمره و هر اخطار {{ $scoringSettings['warning_coefficient'] }} نمره کسر می‌کند.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div>
                    <label for="accident_count" class="form-label">تعداد سانحهها <span class="text-xs text-rose-500">(-{{ fa_num($scoringSettings['accident_coefficient']) }})</span></label>
                    <input id="accident_count" name="accident_count" type="text" inputmode="numeric" required
                        value="{{ fa_input(old('accident_count', $evaluation?->accident_count ?? 0)) }}"
                        class="score-input form-input @error('accident_count') border-rose-400 @enderror" dir="rtl">
                    @error('accident_count')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="near_miss_count" class="form-label">شبه سانحهها <span class="text-xs text-rose-500">(-{{ fa_num($scoringSettings['near_miss_coefficient']) }})</span></label>
                    <input id="near_miss_count" name="near_miss_count" type="text" inputmode="numeric" required
                        value="{{ fa_input(old('near_miss_count', $evaluation?->near_miss_count ?? 0)) }}"
                        class="score-input form-input @error('near_miss_count') border-rose-400 @enderror" dir="rtl">
                    @error('near_miss_count')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="warning_count" class="form-label">تعداد اخطارها <span class="text-xs text-rose-500">(-{{ fa_num($scoringSettings['warning_coefficient']) }})</span></label>
                    <input id="warning_count" name="warning_count" type="text" inputmode="numeric" required
                        value="{{ fa_input(old('warning_count', $evaluation?->warning_count ?? 0)) }}"
                        class="score-input form-input @error('warning_count') border-rose-400 @enderror" dir="rtl">
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
                    <span id="total-preview" class="text-6xl font-black tracking-tight text-white">۰.۰۰</span>
                    <span class="pb-2 text-sm text-slate-400 dark:text-slate-500">از {{ fa_num($maxTotal) }}</span>
                </div>

                <div id="total-message" class="mt-4 rounded-xl bg-white/10 px-3 py-2 text-center text-xs font-medium text-slate-200">
                    نمرات را وارد کنید.
                </div>

                <dl class="mt-6 space-y-2.5 border-t border-white/10 pt-5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">مجموع نمرات</dt>
                        <dd id="breakdown-scores" class="font-semibold text-emerald-300">۰.۰۰</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر سانحهها</dt>
                        <dd id="breakdown-accidents" class="font-semibold text-rose-300">۰.۰۰</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر شبه سانحهها</dt>
                        <dd id="breakdown-near-miss" class="font-semibold text-rose-300">۰.۰۰</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-400 dark:text-slate-500">کسر اخطارها</dt>
                        <dd id="breakdown-warnings" class="font-semibold text-rose-300">۰.۰۰</dd>
                    </div>
                </dl>
            </div>

            <div class="card p-5 text-xs leading-6 text-slate-500 dark:text-slate-400">
                <p class="mb-2 font-bold text-slate-700 dark:text-slate-200">فرمول محاسبه امتیاز این ارزیابی</p>
                <p dir="rtl" class="rounded-lg bg-slate-50 dark:bg-slate-800 px-3 py-2 text-center font-mono text-[11px] text-slate-600 dark:text-slate-300">
                    total = theory + practical + ethics − (accidents × {{ fa_num($scoringSettings['accident_coefficient']) }}) − (near misses × {{ fa_num($scoringSettings['near_miss_coefficient']) }}) − (warnings × {{ fa_num($scoringSettings['warning_coefficient']) }})
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

            const toLatinDigits = (value) => String(value)
                .replace(/[\u0660-\u0669]/g, (digit) => String(digit.charCodeAt(0) - 0x0660))
                .replace(/[\u06F0-\u06F9]/g, (digit) => String(digit.charCodeAt(0) - 0x06F0));

            const number = (input) => {
                if (!input || input.value.trim() === '') {
                    return 0;
                }

                const value = parseFloat(toLatinDigits(input.value));

                return Number.isFinite(value) ? value : 0;
            };

            const format = (value) => window.toPersianDigits
                ? window.toPersianDigits(value.toFixed(2))
                : value.toFixed(2);

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
