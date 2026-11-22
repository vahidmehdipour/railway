@extends('layouts.app')

@section('title', 'مدیریت ظاهر')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">مدیریت ظاهر</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">نام سامانه، متن پاورقی و پسزمینه صفحه ورود را از این بخش تغییر دهید.</p>
        </div>

        <form method="POST" action="{{ route('appearance.update') }}" class="card overflow-hidden">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-100 dark:border-slate-700 px-6 py-5">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">اطلاعات نمایشی</h3>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">این مقادیر در سربرگ، منو، صفحات PDF و صفحه ورود نمایش داده میشوند.</p>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <label for="app_name" class="form-label">نام سامانه</label>
                    <input id="app_name" name="app_name" type="text" required maxlength="120"
                        value="{{ old('app_name', $appearance['app_name']) }}"
                        class="form-input @error('app_name') border-rose-400 @enderror">
                    @error('app_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="footer_text" class="form-label">متن پاورقی</label>
                    <input id="footer_text" name="footer_text" type="text" required maxlength="255"
                        value="{{ old('footer_text', $appearance['footer_text']) }}"
                        class="form-input @error('footer_text') border-rose-400 @enderror">
                    @error('footer_text')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="copyright_text" class="form-label">متن حق کپی</label>
                    <input id="copyright_text" name="copyright_text" type="text" required maxlength="255"
                        value="{{ old('copyright_text', $appearance['copyright_text']) }}"
                        class="form-input @error('copyright_text') border-rose-400 @enderror">
                    @error('copyright_text')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="login_background" class="form-label">پسزمینه صفحه ورود</label>
                    <select id="login_background" name="login_background" class="form-select @error('login_background') border-rose-400 @enderror">
                        @foreach ($backgrounds as $value => $label)
                            <option value="{{ $value }}" @selected(old('login_background', $appearance['login_background']) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('login_background')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 dark:border-slate-700 px-6 py-4">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection
