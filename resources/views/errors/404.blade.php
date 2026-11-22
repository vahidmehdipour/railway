<x-error-layout
    code="۴۰۴"
    title="صفحه یافت نشد"
    message="صفحهای که به دنبال آن هستید وجود ندارد یا جابجا شده است."
>
    <a href="{{ route('dashboard') }}" class="btn-primary">بازگشت به داشبورد</a>
    <a href="{{ url()->previous() }}" class="btn-secondary">بازگشت به صفحه قبل</a>
</x-error-layout>
