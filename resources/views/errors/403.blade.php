<x-error-layout
    code="۴۰۳"
    title="دسترسی غیرمجاز"
    message="شما اجازه مشاهده این صفحه را ندارید. در صورتی که تصور میکنید مشکلی وجود دارد، با مدیر سامانه تماس بگیرید."
>
    <a href="{{ route('dashboard') }}" class="btn-primary">بازگشت به داشبورد</a>
    <a href="{{ url()->previous() }}" class="btn-secondary">بازگشت به صفحه قبل</a>
</x-error-layout>
