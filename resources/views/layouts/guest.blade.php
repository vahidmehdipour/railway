<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ورود') | {{ appearance_setting('app_name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script>
        (() => {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', stored ? stored === 'dark' : prefersDark);
            } catch (error) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $loginBackgrounds = [
        'slate' => 'bg-slate-100 dark:bg-slate-950',
        'indigo' => 'bg-gradient-to-br from-indigo-100 via-sky-100 to-slate-100 dark:from-slate-950 dark:via-indigo-950 dark:to-slate-950',
        'emerald' => 'bg-gradient-to-br from-emerald-100 via-teal-100 to-slate-100 dark:from-slate-950 dark:via-emerald-950 dark:to-slate-950',
        'sunset' => 'bg-gradient-to-br from-amber-100 via-rose-100 to-slate-100 dark:from-slate-950 dark:via-rose-950 dark:to-slate-950',
    ];

    $loginBackground = $loginBackgrounds[appearance_setting('login_background')] ?? $loginBackgrounds['slate'];
@endphp
<body class="h-full font-sans text-slate-800 antialiased dark:text-slate-100 {{ $loginBackground }}">
    <div x-data="{
        dark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            try { localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } catch (error) {}
        }
    }" class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="mb-6 flex flex-col items-center gap-3 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="لوگوی راهآهن جمهوری اسلامی ایران" class="h-16 w-auto object-contain">
            <div>
                <p class="text-lg font-extrabold text-slate-900 dark:text-white">{{ appearance_setting('app_name') }}</p>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">راهآهن جمهوری اسلامی ایران</p>
            </div>
        </div>

        <div class="w-full max-w-md">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-200/60 dark:border-slate-700 dark:bg-slate-800 dark:shadow-none">
                <div class="h-1.5 bg-gradient-to-l from-indigo-500 via-sky-400 to-emerald-400"></div>
                <div class="p-7 sm:p-9">
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-5 text-center text-xs text-slate-400 dark:text-slate-500">
                {{ appearance_setting('copyright_text') }}
            </p>
        </div>

        <button type="button" @click="toggleDark()" class="fixed bottom-4 left-4 z-50 inline-flex size-10 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 shadow-lg transition hover:bg-slate-50 hover:text-indigo-600 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700" aria-label="تغییر حالت نمایش">
            <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
            </svg>
            <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-5" style="display: none">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
            </svg>
        </button>
    </div>
    @stack('scripts')
</body>
</html>
