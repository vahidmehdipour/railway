<x-error-layout
    code="۵۰۰"
    title="خطای سرور"
    message="متأسفانه مشکلی در سامانه رخ داد. لطفاً لحظاتی بعد دوباره تلاش کنید."
>
    <a href="{{ url()->current() }}" class="btn-primary">تلاش مجدد</a>
    <a href="{{ route('dashboard') }}" class="btn-secondary">بازگشت به داشبورد</a>
</x-error-layout>
