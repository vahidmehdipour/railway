# کار با Git برای نصب و بروزرسانی

> اگر از **cPanel Git™ Version Control** استفاده میکنی، به جای این فایل از [`CPANEL-GIT.md`](CPANEL-GIT.md) پیروی کن. در این فایل فقط روند `git archive` / ZIP توضیح داده شده است.

با Git دیگر نیازی به فهرست کردن دستی فایلها برای ZIP نیست. `.gitignore` و `.gitattributes` طوری تنظیم شدند که:

- فایلهای لوکال مثل `.env`, `database/database.sqlite`, `node_modules`, `public/`, `tests/` وارد بسته نمیشوند.
- `vendor/` و `deploy/public_html/build/` چون هاست Composer ندارد، **وارد بسته میشوند**.
- `deploy/public_html/` همان نسخهای است که باید داخل Document Root زیردامنه کپی شود.

---

## ۱) آمادهسازی اولیه (یک بار)

در پوشه 프로ژه:

```powershell
git init
git config user.name "Vahid"
git config user.email "vahid@example.com"
git add .
git status --short
```

باید مطمئن شوی `vendor/` در لیست فایلهای اضافهشده هست:

```powershell
git ls-files | Select-String "vendor/autoload.php"
```

اگر خروجی داشت، یعنی vendor وارد Git شده و در ZIP هم خواهد بود.

سپس commit و tag اول:

```powershell
git commit -m "Initial deployable release"
git tag -a v1.0.0 -m "First release"
```

---

## ۲) ساخت ZIP تمیز ازGit

ZIP را **خارج از پوشه پروژه** بساز، مثلاً روی Desktop:

```powershell
git archive --format=zip --output="$env:USERPROFILE\Desktop\railway-v1.0.0.zip" v1.0.0
```

این فایل شامل دقیقاً همان چیزهایی است که باید در `/home/USERNAME/railway/` آپلود شود، و چیزی که نبایدباشد داخلش نیست.

---

## ۳) نصب اول روی هاست

همان مراحل `DEPLOY.md` را اجرا کن:

1. ZIP را در `/home/USERNAME/railway/` استخراج کن.
2. `deploy/.env.host` را به `/home/USERNAME/railway/.env` rename کن.
3. محتویات `deploy/public_html/` را به Document Root زیردامنه کپی کن.
4. دسترسی `storage` و `bootstrap/cache` را قابل نوشتن کن.
5. باز کن:

```text
https://crm.lemooshop.ir/_setup.php?token=a703d767de202e561258250e07233d0f
```

6. بعد از `DONE`، فایل `_setup.php` را حذف کن.

---

## ۴) روند بروزرسانی (هر بار که کد عوض شد)

### الف) لوکال

```powershell
npm run build
composer install --no-dev --optimize-autoloader
git add .
git commit -m "Update: توضیح تغییرات"
git tag -a v1.0.1 -m "Update 1"
git archive --format=zip --output="$env:USERPROFILE\Desktop\railway-v1.0.1.zip" v1.0.1
```

هر بار شماره tag را زیاد کن: `v1.0.2`, `v1.0.3` و ...

### ب) هاست

1. ZIP جدید را استخراج کن **روی** `/home/USERNAME/railway/` و فایلها را overwrite کن.
2. ⚠️ **فایل `.env` را overwrite نکن.** دیتابیس و تنظیمات هاست دستنخورده بماند.
3. ⚠️ پوشه `storage/` را حذف نکن؛ فقط فایلهای کد آپدیت میشوند.
4. محتویات `deploy/public_html/` را دوباره به Document Root کپی کن تا `index.php`, `build/` و فایل `_update.php` آپدیت شوند. ⚠️ `index.php` را حتماً overwrite کن: این فایل مسیر public لاراول را روی Document Root تنظیم میکند تا `build/manifest.json` پیدا شود.
5. باز کن:

```text
https://crm.lemooshop.ir/_update.php?token=a703d767de202e561258250e07233d0f
```

6. بعد از `UPDATE DONE`، فایل `_update.php` را حذف کن.

`_update.php` فقط migrationهای جدید را اجرا و cacheها را پاک میکند؛ **seed نمیکند**، پس تنظیمات و دیتای تو پاک نمیشود.

---

## ۵) برگشت به نسخه قبلی (Rollback)

اگر آپدیت مشکلی داشت،tag قبلی را زیپ کن و همان مراحل بالا را اجرا کن:

```powershell
git archive --format=zip --output="$env:USERPROFILE\Desktop\railway-v1.0.0.zip" v1.0.0
```

سپس همان فایل قدیمی را روی هاست بریز و `_update.php` را اجرا کن.

---

## ۶) نکات مهم

- هرگز `.env` یا `database/database.sqlite` را commit نکن.
- `vendor` عمداً وارد Git شده، چون هاست Composer ندارد. حجم ZIP بالاتر میرود ولی نصب ساده میماند.
- `deploy/public_html/_setup.php` فقط برای نصب اول است.
- `deploy/public_html/_update.php` فقط برای بروزرسانیهاست.
- توکن به اندازهای طولانی و امن است؛ بعد از هر اجرا فایل مربوطه را حذف کن.

---

## خلاصه

```powershell
# بار اول
git init; git add .; git commit -m "Initial"; git tag v1.0.0
git archive --format=zip --output=~/Desktop/railway-v1.0.0.zip v1.0.0

# هر آپدیت
npm run build
composer install --no-dev --optimize-autoloader
git add .; git commit -m "Update"; git tag v1.0.1
git archive --format=zip --output=~/Desktop/railway-v1.0.1.zip v1.0.1
```
