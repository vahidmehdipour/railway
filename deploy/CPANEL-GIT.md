# استقرار با cPanel Git™ Version Control

این روش جای ZIP دستی را میگیرد. نیازی به SSH یا Composer روی هاست نیست.

## ساختار روی هاست

```text
/home3/hwuxndqg/
├── rail/              ← Repository Path (ریشه پروژه لاراول)
│   ├── app/
│   ├── bootstrap/
│   ├── vendor/
│   ├── .env                   ← دستی ساخته میشود، وارد Git نمیشود
│   └── deploy/
│       ├── public_html/       ← توسط .cpanel.yml کپی میشود
│       └── tools/             ← هرگز کپی نمیشود؛ فقط منبع اسکریپتهاست
│           ├── _update.php    ← فقط اگر حذف شده باشد به Document Root برگردانده میشود
│           ├── _setup.php     ← فقط برای نصب مجدد دستی کپی شود
│           ├── _envcheck.php
│           └── _diag.php
│
└── rail.lemooshop.ir/         ← Document Root زیردامنه
    ├── index.php
    ├── .htaccess              ← دستنخورده میماند (تنظیم PHP 8.4 اینجاست)
    ├── build/
    └── _update.php            ← بعد از اجرا حذف میشود و در deploy بعدی برمیگردد
```

`index.php` سفارشی، پوشه `rail` را بهعنوان ریشه پروژه پیدا میکند و مسیر public لاراول را روی Document Root تنظیم میکند.

> ⚠️ اگر قبلاً نسخهٔ دستی در `/home3/hwuxndqg/railway` مستقر کردهاید، اسکریپتهای deploy مسیر را بهصورت ثابت `/home3/hwuxndqg/rail` میگیرند و دیگر پوشهٔ قدیمی را جستجو نمیکنند. حتماً `.env` را در `/home3/hwuxndqg/rail/.env` بسازید و `Deploy HEAD Commit` را دوباره بزنید تا `index.php` جدید کپی شود.

---

## ۰) دسترسی به ریپو خصوصی (Deploy Key)

چون ریپو خصوصی شد، cPanel برای Clone و `Update from Remote` به کلید دسترسی نیاز دارد. نیازی به SSH یا ترمینال نیست؛ همان صفحه cPanel کافی است.

1. در **cPanel → Git™ Version Control**، وارد تب/بخش **Deploy Keys** شو و **Create Key** بزن.
2. کلید عمومی (`ssh-rsa ...` یا `-----BEGIN OPENSSH PUBLIC KEY-----`) را کپی کن.
3. در GitHub به **Settings → Deploy keys → Add deploy key** برو:
   - Title: `cpanel-rail`
   - Key: همان کلید کپی شده
   - تیک **Allow write access** را نزن (ما فقط Pull میکنیم).
4. برگرد به cPanel و کلید را تأیید/فعال کن.
5. حالا Clone و `Update from Remote` بدون مشکل کار میکند.

> اگر cPanel کلید را قبول نکرد، Clone URL را به حالت SSH امتحان کن:
> `git@github.com:vahidmehdipour/railway.git`
> (همان کلید عمومی باید در GitHub به‌عنوان Deploy Key ثبت شده باشد.)
> ولی Deploy Key امنتر است، چون token در تنظیمات cPanel ذخیره میشود.

---

### اگر ریپوی خصوصی در cPanel اصلاً کار نکرد

می‌توانی موقتاً ریپو را Public کنی، فقط در همان بازهٔ Pull/Deploy:

1. GitHub → Settings → General → Danger Zone → Change visibility → Make public
2. cPanel → Update from Remote → Deploy HEAD Commit
3. اگر `_update.php` روی هاست هست، آن را اجرا کن (بعد از حذف شدنش در deploy برگردانده میشود)
4. بلافاصله ریپو را دوباره Private کن

⚠️ در این بازه هر کسی می‌تواند کد را ببیند یا Fork کند و Forkها بعد از Private شدن هم ممکن است عمومی بمانند. چون در ریپو `.env` یا رمز نیست، ریسک اصلی افشای کد است، نه اطلاعات حساس. این بازه را کوتاه نگه دار.

---

## ۱) نصب اول (یک بار)

1. در cPanel وارد **Git™ Version Control** شو.
2. **Create** بزن و گزینه Clone را فعال کن.
3. Clone URL:

```text
https://github.com/vahidmehdipour/railway.git
```

4. Repository Path:

```text
/home3/hwuxndqg/rail
```

5. Repository Name: مثلاً `rail`.
6. Branch را `main` انتخاب کن.
7. بعد از clone، وارد **Manage → Pull or Deploy** شو:
   - **Update from Remote**
   - **Deploy HEAD Commit**

فایل `.cpanel.yml` برای تو کپی `deploy/public_html/.` به Document Root را انجام میدهد.

### ساخت `.env`

در File Manager داخل:

```text
/home3/hwuxndqg/rail/.env
```

محتوای فایل لوکال `deploy/.env.rail` را بذار. این فایل عمداً وارد Git نمیشود چون حاوی رمز دیتابیس است.

مقادیر مهم:

```dotenv
APP_URL=https://rail.lemooshop.ir
DB_DATABASE=hwuxndqg_rail
DB_USERNAME=hwuxndqg_root
DB_PASSWORD=**********
APP_KEY=base64:...    ← همان کلیدی که در deploy/.env.rail است
```

سپس:

```text
/home3/hwuxndqg/rail/storage
/home3/hwuxndqg/rail/bootstrap/cache
```

را قابل نوشتن ( writable) کن.

### اجرای نصب (فقط نصب اول)

اسکریپت نصب **دیگر بهصورت خودکار deploy نمیشود**. فقط در نصب اول، فایل `deploy/tools/_setup.php` را از File Manager داخل ریپو باز کن و محتوایش را بهآدرس زیر کپی کن:

```text
/home3/hwuxndqg/rail.lemooshop.ir/_setup.php
```

سپس باز کن:

```text
https://rail.lemooshop.ir/_setup.php?token=a703d767de202e561258250e07233d0f
```

اسکریپت قبل از اجرای migrate، مسیر ریشه و نام دیتابیس را چاپ میکند و اگر `DB_NAME` برابر `hwuxndqg_rail` نباشد، متوقف میشود. پس حتماً خروجی آن را ببین.

بعد از `DONE`، فایل `_setup.php` خودش حذف میشود.

> 🚫 **هرگز `_setup.php` را دوباره اجرا نکن.** این اسکریپت `db:seed` اجرا میکند و ایستگاهها و پروفایلهای پیشفرض را برگردانده، تغییرات تو را بازنویسی میکند. از نسخهٔ جدید اضافه شده محافظت میکند: اگر دیتابیس پر باشد، اسکریپت متوقف میشود و پیام `already contains installed data` میدهد. در آن حالت فقط از `_update.php` استفاده کن.

---

## ۲) بروزرسانی (هر بار)

روی لوکال:

```powershell
npm run build
git add .
git commit -m "Update: توضیح"
git push origin main
```

روی cPanel:

1. **Git Version Control → Manage → Pull or Deploy**
2. **Update from Remote**
3. **Deploy HEAD Commit**
4. `_update.php` بهصورت خودکار فقط در صورتی برگردانده میشود که **حذف شده باشد**. اگر بعد از اجرای قبلی حذف شده، باز کن:

```text
https://rail.lemooshop.ir/_update.php?token=a703d767de202e561258250e07233d0f
```

5. بعد از `UPDATE DONE`، `_update.php` دوباره حذف میشود و در deploy بعدی برمیگردد.

> ✅ `_update.php` فقط `migrate` و پاکسازی کش را اجرا میکند. **هیچ `db:seed`، `migrate:fresh` یا `db:wipe` در آن نیست**، پس دادههایی که وارد کردهای دستنخورده میمانند.
> 🚫 بهجای آن `_setup.php` را اجرا نکن؛ نصب مجدد همان چیزی است که باعث بازگشت ایستگاهها و پروفایلهای پیشفرض میشود.
> ℹ️ `_setup.php`، `_envcheck.php` و `_diag.php` از این به بعد deploy نمیشوند و در `deploy/tools/` نگه داشته شدهاند تا در صورت نیاز دستی کپی شوند.

⚠️ هرگز `.env` را overwrite نکن. `.cpanel.yml` محتویات `deploy/public_html/` را کپی میکند و `_update.php` را تنها در صورت نبودنش از `deploy/tools/` میآورد، پس `.env` و `storage` دستنخورده میمانند.

---

## ۳) نسخهٔ PHP روی هاست (مهم)

اپلیکیشن به **PHP 8.4 یا بالاتر** نیاز دارد (`vendor/composer/platform_check.php` حداقل `8.4.1` را میخواهد). پیش از این، `.cpanel.yml` با هر `Deploy HEAD Commit` فایل `.htaccess` را بازنویسی میکرد؛ cPanel تنظیم PHP 8.4 را در همان فایل ذخیره میکند، پس با بازنویسی، دوباره به PHP 8.1 پیشفرض برمیگشتی و مجبور بودی دوباره 8.4 بزنی.

از نسخهٔ جدید، فایل `.htaccess` در ریپو به `.htaccess.dist` تغییر نام یافته و `.cpanel.yml` فقط زمانی که `.htaccess` روی هاست وجود ندارد آن را میسازد (`cp -n`). یعنی:

1. **یک بار** در cPanel → **MultiPHP Manager** (یا **Select PHP Version**) برای `rail.lemooshop.ir` نسخهٔ **8.4** را انتخاب کن.
2. بعد از آن هر بار فقط `Update from Remote` + `Deploy HEAD Commit` بزن؛ نیازی به تغییر PHP نیست.

اگر روزی `.htaccess` را دستی از File Manager پاک کردی، بعد از deploy دوباره همان یک بار گزینهٔ 8.4 را بزن.

---

## ۴) نکات امنیتی

- ریپو **Private** است و فقط از طریق Deploy Key قابل Clone است.
- `.env`، `.env.host`، `.env.rail` و فایلهای ZIP وارد ریپو نمیشوند.
- رمز دیتابیس را فقط در `.env` روی هاست نگه دار.
- Deploy Key فقط دسترسی خواندن دارد؛ نیازی به `Allow write access` نیست.

---

## ۵) نسخهها و برگشت از آپدیت

نسخهٔ جاری **`1.2.0`** است و در دو جا نمایش داده میشود:

- زیر دکمهٔ «خروج از حساب» در منوی کناری
- زیر متن کپی رایت در صفحهٔ ورود

قواعد Semantic Versioning:

| تغییر | مثال | شمارهٔ بعدی |
| --- | --- | --- |
| رفع باگ | `1.2.0` → `1.2.1` | عدد سوم |
| اضافه شدن قابلیت | `1.2.0` → `1.3.0` | عدد دوم |
| تغییر اساسی/ناسازگار | `1.2.0` → `2.0.0` | عدد اول |

⚠️ خودِ عدد نسخه بهتنهایی کاری را برنمیگرداند؛ **تگ Git** است که امکان برگشت میدهد:

- `v1.0.0` → آخرین وضعیت سالم قبل از انتشار ۱.۱ (کامیت `4a44b32`)
- `v1.1.0` → انتشار قبلی (کامیت `b14af57`)
- `v1.2.0` → انتشار جاری

اگر بعد از آپدیت مشکلی پیش آمد:

1. در cPanel → Git Version Control → **Manage**، تگ/کامیت `v1.0.0` را checkout کن (یا_branch مربوط به آن را انتخاب کن).
2. **Deploy HEAD Commit** بزن.
3. دیتابیس دستنخورده میماند؛ فقط کدها به نسخهٔ قبل برمیگردند. چون migrationها فقط اضافه میشوند، نیازی به برگشت دیتابیس نیست.

برای انتشار بعدی: عدد `config/app.php` را بهروز کن، commit بگیر، سپس `git tag v1.3.0` و `git push origin v1.3.0` را اجرا کن.
