@php
    $navigation = [
        [
            'label' => 'داشبورد',
            'route' => 'dashboard',
            'active' => request()->routeIs('dashboard'),
            'icon' => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        ],
        [
            'label' => 'پرسنل',
            'route' => 'personnels.index',
            'active' => request()->routeIs('personnels.index', 'personnels.create', 'personnels.edit'),
            'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
        [
            'label' => 'ارزیابی‌ها',
            'route' => 'evaluations.index',
            'active' => request()->routeIs('evaluations.*'),
            'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z',
        ],
        [
            'label' => 'دوره‌های ارزیابی',
            'route' => 'periods.index',
            'active' => request()->routeIs('periods.*'),
            'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        ],
        ...(auth()->user()?->can(\App\Support\Permissions::MANAGE_SETTINGS) ? [[
            'label' => 'تنظیمات سیستم',
            'route' => 'settings.index',
            'active' => request()->routeIs('settings.*'),
            'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
        ]] : []),
        ...(auth()->user()?->can(\App\Support\Permissions::VIEW_ACTIVITY_LOG) ? [[
            'label' => 'لاگ سیستم',
            'route' => 'activity-log.index',
            'active' => request()->routeIs('activity-log.*'),
            'icon' => 'M12 6.75v5.25l3.75 2.25m6.75-3.75a9 9 0 11-18 0 9 9 0 0118 0z',
        ]] : []),
        ...(auth()->user()?->can(\App\Support\Permissions::MANAGE_SETTINGS) ? [[
            'label' => 'مدیریت ظاهر',
            'route' => 'appearance.edit',
            'active' => request()->routeIs('appearance.*'),
            'icon' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z',
        ]] : []),
    ];

    $routePermissions = [
        'dashboard' => \App\Support\Permissions::VIEW_DASHBOARD,
        'personnels.index' => \App\Support\Permissions::VIEW_PERSONNELS,
        'evaluations.index' => \App\Support\Permissions::VIEW_EVALUATIONS,
        'periods.index' => \App\Support\Permissions::VIEW_PERIODS,
        'settings.index' => \App\Support\Permissions::MANAGE_SETTINGS,
        'activity-log.index' => \App\Support\Permissions::VIEW_ACTIVITY_LOG,
        'appearance.edit' => \App\Support\Permissions::MANAGE_SETTINGS,
    ];

    $adminNavigation = [
        [
            'label' => 'مدیریت کاربران',
            'route' => 'users.index',
            'active' => request()->routeIs('users.*'),
            'icon' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        ],
    ];

    $accountNavigation = [
        [
            'label' => 'پروفایل من',
            'route' => 'profile.edit',
            'active' => request()->routeIs('profile.*'),
            'icon' => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z',
        ],
    ];
@endphp

{{-- Mobile overlay --}}
<div
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
    @click="sidebarOpen = false"
></div>

<aside
    class="fixed inset-y-0 right-0 z-50 flex w-72 transform flex-col bg-slate-900 text-slate-300 shadow-2xl transition-transform duration-200 ease-in-out dark:bg-slate-950 lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
>
    {{-- Company logo --}}
    <div class="flex h-24 shrink-0 items-center gap-3 border-b border-white/10 px-5">
        <img src="{{ asset('images/logo.png') }}" alt="لوگوی راهآهن جمهوری اسلامی ایران" class="h-12 w-auto shrink-0 object-contain">
        <div class="min-w-0">
            <p class="truncate text-sm font-bold text-white">{{ appearance_setting('app_name') }}</p>
            <p class="truncate text-xs text-slate-400 dark:text-slate-500">سیستم ارزیابی پرسنل</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">منوی اصلی</p>

        @foreach ($navigation as $item)
            @can($routePermissions[$item['route']] ?? $item['route'])
                <x-nav-link :item="$item" />
            @endcan
        @endforeach

        @role('super-admin')
            @foreach ($adminNavigation as $item)
                <x-nav-link :item="$item" />
            @endforeach
        @endrole

        @foreach ($accountNavigation as $item)
            <x-nav-link :item="$item" />
        @endforeach
    </nav>

    {{-- User / logout --}}
    <div class="shrink-0 border-t border-white/10 p-4">
        <div class="flex items-center gap-3 rounded-xl bg-white/5 p-3">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-slate-700 text-sm font-bold text-white">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">
                    {{ auth()->user()->name }}
                    @role('super-admin')
                        <span class="mr-1 rounded-full bg-indigo-500/20 px-2 py-0.5 text-[10px] font-medium text-indigo-200">مدیر سیستم</span>
                    @endrole
                </p>
                <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 px-3 py-2 text-sm font-medium text-slate-300 transition hover:border-rose-400/40 hover:bg-rose-500/10 hover:text-rose-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                </svg>
                خروج از حساب
            </button>
        </form>
    </div>
</aside>
