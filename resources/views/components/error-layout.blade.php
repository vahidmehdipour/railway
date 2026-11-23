@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="fa" dir="rtl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ $code }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 font-sans text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="لوگوی راهآهن جمهوری اسلامی ایران" class="mb-6 h-16 w-auto object-contain">

        <p class="text-6xl font-black tracking-tight text-indigo-600 dark:text-indigo-400">{{ $code }}</p>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900 dark:text-white">{{ $title }}</h1>
        <p class="mt-2 max-w-md text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $message }}</p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
