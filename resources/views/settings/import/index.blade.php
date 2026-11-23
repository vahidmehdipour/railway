@extends('layouts.app')

@section('title', 'وارد کردن پرسنل از اکسل')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 dark:text-indigo-300 hover:text-indigo-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                </svg>
                بازگشت به تنظیمات
            </a>
            <h2 class="mt-3 text-xl font-bold text-slate-900 dark:text-white">وارد کردن پرسنل از فایل اکسل</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                فایل اکسل یا CSV پرسنل را بارگذاری کرده، پیشنمایش را بررسی و سپس اطلاعات را ذخیره کنید.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 px-5 py-4 text-sm text-rose-700 dark:text-rose-200">
                <p class="font-bold">لطفاً موارد زیر را برطرف کنید:</p>
                <ul class="mt-2 list-disc pr-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-5">
            {{-- Upload form --}}
            <form method="POST" action="{{ route('settings.import.preview') }}" enctype="multipart/form-data" class="card space-y-5 p-5 xl:col-span-3">
                @csrf

                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">۱. بارگذاری فایل</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">فرمتهای مجاز: xlsx، csv، xml و اکسل XML (حداکثر ۵ مگابایت و ۵۰۰ ردیف).</p>
                </div>

                <div>
                    <label for="file" class="form-label">فایل پرسنل <span class="text-rose-500">*</span></label>
                    <input id="file" name="file" type="file" required accept=".xlsx,.xls,.csv,.txt,.xml"
                        class="form-input @error('file') border-rose-400 @enderror">
                    @error('file')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="mode" class="form-label">نوع اطلاعات فایل</label>
                    <select id="mode" name="mode" class="form-input" required>
                        <option value="auto" @selected(old('mode', 'auto') === 'auto')>تشخیص خودکار بر اساس ستونها</option>
                        <option value="personnel" @selected(old('mode') === 'personnel')>فقط اطلاعات پرسنل</option>
                        <option value="scores" @selected(old('mode') === 'scores')>پرسنل به همراه امتیازات ارزیابی</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        در حالت خودکار، وجود هر یک از ستونهای امتیاز باعث ثبت ارزیابی نیز میشود.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="btn-primary justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        بارگذاری و پیشنمایش
                    </button>
                    <a href="{{ route('settings.import.template', ['mode' => 'personnel']) }}" class="btn-secondary justify-center">الگوی پرسنل</a>
                    <a href="{{ route('settings.import.template', ['mode' => 'scores']) }}" class="btn-secondary justify-center">الگوی پرسنل و امتیاز</a>
                </div>
            </form>

            {{-- Guide --}}
            <aside class="card p-5 xl:col-span-2">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">راهنمای استفاده</h3>
                <ol class="mt-4 space-y-3 text-xs leading-6 text-slate-600 dark:text-slate-300">
                    <li><span class="font-bold text-indigo-600 dark:text-indigo-300">۱.</span> الگوی مناسب را دانلود و نام ستونها را حفظ کنید.</li>
                    <li><span class="font-bold text-indigo-600 dark:text-indigo-300">۲.</span> نقشها، دورهها و ایستگاههای مورد استفاده را ابتدا در تنظیمات تعریف کنید.</li>
                    <li><span class="font-bold text-indigo-600 dark:text-indigo-300">۳.</span> فایل را بارگذاری کنید؛ هیچ اطلاعاتی قبل از تأیید پیشنمایش ذخیره نمیشود.</li>
                    <li><span class="font-bold text-indigo-600 dark:text-indigo-300">۴.</span> برای پرسنل تکراری (کد ملی یا نام و نام خانوادگی یکسان)، پروفایل جدیدی ساخته نمیشود و امتیازات به همان پروفایل اضافه میشود.</li>
                </ol>

                <h4 class="mt-6 text-xs font-bold text-slate-800 dark:text-slate-100">ستونهای الزامی</h4>
                <div class="mt-2 overflow-hidden rounded-xl border border-slate-100 dark:border-slate-700">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50/70 dark:bg-slate-800/70 text-slate-500 dark:text-slate-400">
                            <tr>
                                <th class="table-th">ستون</th>
                                <th class="table-th">توضیح</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            <tr><td class="table-td font-semibold">نام / نام خانوادگی</td><td class="table-td">الزامی</td></tr>
                            <tr><td class="table-td font-semibold">نقش</td><td class="table-td">باید با نام فارسی یا انگلیسی یک نقش موجود برابر باشد.</td></tr>
                            <tr><td class="table-td font-semibold">سال ورود</td><td class="table-td">مثلاً ۱۳۹۸</td></tr>
                            <tr><td class="table-td font-semibold">کد ملی</td><td class="table-td">۱۰ رقم؛ برای تشخیص پرسنل تکراری بسیار مهم است.</td></tr>
                            <tr><td class="table-td font-semibold">وضعیت</td><td class="table-td">فعال (پیشفرض) یا غیرفعال</td></tr>
                        </tbody>
                    </table>
                </div>

                <h4 class="mt-5 text-xs font-bold text-slate-800 dark:text-slate-100">ستونهای اختیاری امتیاز</h4>
                <p class="mt-2 text-xs leading-6 text-slate-600 dark:text-slate-300">
                    تاریخ ارزیابی (شمسی یا میلادی)، دوره ارزیابی (اگر خالی باشد دوره فعال استفاده میشود)، ایستگاه،
                    نمره تئوری، نمره عملی، نمره اخلاق، تعداد سانحه، تعداد شبه سانحه، تعداد اخطار و توضیحات.
                    اعداد فارسی هم پذیرفته میشوند و به صورت لاتین در پایگاه داده ذخیره میگردند.
                </p>
            </aside>
        </div>
    </div>
@endsection
