<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'داشبورد') | {{ appearance_setting('app_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icon.svg">
    <link rel="apple-touch-icon" href="/icon.svg">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <script>
        (() => {
            try {
                const stored = localStorage.getItem('theme');
                document.documentElement.classList.toggle('dark', stored === 'dark');
            } catch (error) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/behzadi/persianDatepicker@master/css/persianDatepicker-default.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/behzadi/persianDatepicker@master/js/persianDatepicker.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.toPersianDigits = function (value) {
            const digits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

            return String(value).replace(/\d/g, (digit) => digits[Number(digit)]);
        };

        window.gregorianToJalali = function (gy, gm, gd) {
            const g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
            let jy = gy <= 1600 ? 0 : 979;
            gy -= gy <= 1600 ? 621 : 1600;

            const gy2 = gm > 2 ? gy + 1 : gy;
            let days = (365 * gy)
                + Math.floor((gy2 + 3) / 4)
                - Math.floor((gy2 + 99) / 100)
                + Math.floor((gy2 + 399) / 400)
                - 80
                + gd
                + g_d_m[gm - 1];

            jy += 33 * Math.floor(days / 12053);
            days %= 12053;
            jy += 4 * Math.floor(days / 1461);
            days %= 1461;

            if (days > 365) {
                jy += Math.floor((days - 1) / 365);
                days = (days - 1) % 365;
            }

            const jm = days < 186 ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
            const jd = 1 + (days < 186 ? days % 31 : (days - 186) % 30);

            return [jy, jm, jd];
        };

        window.initShamsiDatepicker = function (hiddenId, pickerId, previewId) {
            const hidden = document.getElementById(hiddenId);
            const picker = document.getElementById(pickerId);
            const preview = document.getElementById(previewId);

            if (!hidden || !picker) {
                return;
            }

            const pad = (value) => String(value).padStart(2, '0');
            const updatePreview = () => {
                if (preview) {
                    preview.textContent = picker.value ? `شمسی: ${window.toPersianDigits(picker.value)}` : 'شمسی: —';
                }
            };

            const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(hidden.value);
            let selectedDate;

            if (match) {
                const [jy, jm, jd] = window.gregorianToJalali(Number(match[1]), Number(match[2]), Number(match[3]));
                selectedDate = `${jy}/${pad(jm)}/${pad(jd)}`;
                picker.value = window.toPersianDigits(selectedDate);
            }

            if (window.jQuery && window.jQuery.fn.persianDatepicker) {
                window.jQuery(picker).persianDatepicker({
                    formatDate: 'YYYY/0M/0D',
                    persianNumbers: true,
                    isRTL: true,
                    showGregorianDate: false,
                    selectedDate,
                    onSelect: function () {
                        const gregorian = window.jQuery(picker).attr('data-gDate');

                        if (gregorian) {
                            const [gy, gm, gd] = gregorian.split('/');
                            hidden.value = `${gy}-${pad(gm)}-${pad(gd)}`;
                        }

                        picker.value = window.toPersianDigits(picker.value);
                        updatePreview();
                    },
                });
            }

            picker.addEventListener('input', updatePreview);
            picker.addEventListener('change', updatePreview);
            updatePreview();
        };
    </script>
</head>
<body class="h-full bg-slate-100 font-sans text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div x-data="{
        sidebarOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            try { localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } catch (error) {}
        }
    }" class="min-h-full">
        <x-sidebar />

        <div class="flex min-h-screen flex-col lg:pr-72">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 border-b border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 backdrop-blur">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button
                        type="button"
                        class="inline-flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-700 lg:hidden"
                        @click="sidebarOpen = true"
                        aria-label="باز کردن منو"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-bold text-slate-900 dark:text-white">@yield('title', 'داشبورد')</h1>
                        <p class="hidden truncate text-xs text-slate-400 dark:text-slate-500 sm:block">{{ appearance_setting('app_name') }}</p>
                    </div>

                    <div class="mr-auto flex items-center gap-3">
                        <button
                            type="button"
                            @click="toggleDark()"
                            class="inline-flex size-10 items-center justify-center rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 transition hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-indigo-600"
                            aria-label="تغییر حالت نمایش"
                        >
                            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                            <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-5" style="display: none">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                        </button>

                        <div class="hidden items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-1 pl-1 pr-3 sm:flex">
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ auth()->user()->name }}</span>
                            <span class="flex size-7 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @php
                    $statusNotices = [
                        'profile-updated' => 'اطلاعات پروفایل با موفقیت ذخیره شد.',
                        'password-updated' => 'رمز عبور با موفقیت تغییر کرد.',
                    ];
                @endphp

                @if (isset($statusNotices[session('status')]))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4500)" x-show="show" x-transition
                        class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium">{{ $statusNotices[session('status')] }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/30 px-4 py-3 text-sm text-rose-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-rose-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 dark:border-slate-700 px-4 py-4 text-center text-xs text-slate-400 dark:text-slate-500 sm:px-6 lg:px-8">
                {{ appearance_setting('footer_text') }} &copy; {{ now()->format('Y') }} — {{ appearance_setting('copyright_text') }}
            </footer>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(() => {});
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
