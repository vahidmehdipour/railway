# نصب نهایی روی زیردامنه `crm.lemooshop.ir`

در این روش نیازی به SSH، Composer روی هاست، Cron یا اجرای دستی `php artisan` نیست. فقط فایلها را در دو جای مشخص کپی میکنی، `.env` را سر جایش میگذاری، یک آدرس موقت را در مرورگر باز میکنی و تمام.

> برای توسعه و بروزرسانیها، استفاده از Git توصیه میشود. دستورالعمل کامل در `deploy/GIT-DEPLOY.md` است. با Git، فایلهای اضافی بهطور خودکار از بسته خارج میشوند و بروزرسانی فقط با یک tag و یک `git archive` انجام میشود.

---

## ۱) ساختار نهایی روی هاست

فرض میگیریم نام کاربری هاستت `USERNAME` است. دو حالت محل Document Root زیردامنه وجود دارد. در پنل هاست، قسمت **Subdomains** یا **Domains** را نگاه کن تا ببینی کدام است:

### حالت معمول cPanel

```text
/home/USERNAME/
├── railway/                     ← کل پروژه لاراول اینجاست
│   ├── app/
│   ├── artisan
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                     ← از deploy/.env.host میآید
│   └── ...
│
└── public_html/
    └── crm/                     ← Document Root زیردامنه crm.lemooshop.ir
        ├── index.php
        ├── .htaccess
        ├── _setup.php
        ├── build/
        ├── images/
        ├── icon.svg
        ├── manifest.json
        ├── robots.txt
        └── service-worker.js
```

### حالت دوم برخی هاستها

```text
/home/USERNAME/
├── railway/                     ← کل پروژه لاراول اینجاست
└── domains/
    └── crm.lemooshop.ir/        ← Document Root زیردامنه
        ├── index.php
        ├── .htaccess
        ├── _setup.php
        ├── build/
        └── ...
```

فرقی نمیکند کدام حالت است. فایل `index.php` و `_setup.php`ای که ساختم **خودشان پوشه `railway` را پیدا میکنند**.

> نکته مهم: `index.php` سفارشی، مسیر public لاراول را روی همین Document Root تنظیم میکند (`usePublicPath`). بنابراین `@vite` مستقیماً همان `build/manifest.json` داخل Document Root را میخواند و **نیازی به کپی کردن `build/` داخل پوشه `railway/public` نیست**. حتماً از `index.php` جدید استفاده کن، چون فایل پیشفرض لاراول این کار را نمیکند.

---

## ۲) اطلاعات دیتابیس

من اطلاعاتی را که دادی در فایل `.env.host` پر کردم:

```dotenv
APP_URL=https://crm.lemooshop.ir
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=hwuxndqg_crm
DB_USERNAME=hwuxndqg_root
DB_PASSWORD=********            ← پر شده است
```

نیازی نیست دوباره جای دیگری واردشان کنی.

### درباره `$table_prefix = 'hwuxndqg_'`

این مورد مربوط به **WordPress** است و در لاراول کاربردی ندارد. چون تو یک دیتابیس جداگانه به نام `hwuxndqg_crm` داری، جداول لاراول نیازی به پیشوند ندارند. من آنرا اعمال نکردم تا مشکلی در raw SQL مربوط به تعمیر `failed_jobs` پیش نیاید.

اگر بعداً خواستی حتماً پیشوند بخورد، بگو تا `config/database.php` و کوئریهای موقت را هم هماهنگ میکنم.

---

## ۳) مرحله به مرحله روی لوکال

### الف) بیلد نهایی

```bash
npm run build
composer install --no-dev --optimize-autoloader
```

### ب) پاکسازی فایلهای لوکال

اینها را قبل از ZIP پاک کن:

```text
bootstrap/cache/*.php
storage/framework/views/*.php
storage/logs/*.log
storage/framework/cache/data/*
```

فقط `.gitignore` داخل این پوشهها بماند.

### ج) ساخت ZIP

فقط موارد زیر را زیپ کن. همینها قرار است در `/home/USERNAME/railway/` قرار بگیرند:

```text
app/
bootstrap/          ← داخل bootstrap/cache فقط .gitignore بماند
config/
database/           ← migrations و seeders، ولی NOT database.sqlite
lang/
resources/
routes/
storage/            ← پوشهها بمانند، محتوی cache/views/logs/sessions پاک شود
vendor/
deploy/             ← شامل .env.host و deploy/public_html
artisan
composer.json
composer.lock
```

`package.json`, `package-lock.json`, `vite.config.js`, `tailwind.config.js` و `README.md` را هم میتوانی زیپ کنی، ولی لازم نیستند.

### مواردی که نباید زیپ شوند و نباید در `/home/USERNAME/railway/` باشند

```text
node_modules/
public/                     ← نسخه نهایی در deploy/public_html است
database/database.sqlite
database/*.sqlite
.env                        ← لوکال است؛ در هاست از deploy/.env.host میسازیم
railway.zip
tests/
.claude/
.idea/
.mcp.json
boost.json
CLAUDE.md
AGENTS.md
bootstrap/cache/*.php
storage/framework/views/*.php
storage/framework/cache/data/*
storage/framework/sessions/*
storage/logs/*.log
.git/
public/hot                  ← اگر public را زیپ کردی، حتماً خارج کن
```

⚠️ فایل `database/database.sqlite` را **حذف نکن**؛ فقط نباید داخل ZIP باشد. دیتای لوکال تو نباید وارد هاست شود. اخطار «The process cannot access the file because it is being used by another process» مربوط به همان فایل است و با خارج کردنش از ZIP حل میشود.

### داخل Document Root زیردامنه هم نباید اینها باشند

یعنی در `public_html/crm/` یا `domains/crm.lemooshop.ir/` فقط محتویات `deploy/public_html/` باشند. موارد زیر نباید آنجا کپی شوند:

```text
app/
bootstrap/
config/
database/
resources/
routes/
storage/
vendor/
artisan
.env
deploy/
node_modules/
database.sqlite
```

---

## ۴) مرحله به مرحله روی هاست

### گام ۱ — دیتابیس

در پنل هاست مطمئن شو دیتابیس و کاربر وجود دارند:

```text
Database: hwuxndqg_crm
Username: hwuxndqg_root
Host:     localhost
```

اگر کاربر ساخته نشده، آنرا بساز و به دیتابیس `hwuxndqg_crm` دسترسی کامل بده.

### گام ۲ — آپلود پروژه

ZIP را در مسیر زیر استخراج کن:

```text
/home/USERNAME/railway/
```

یعنی بعد از استخراج، باید چنین فایلی وجود داشته باشد:

```text
/home/USERNAME/railway/artisan
/home/USERNAME/railway/vendor/autoload.php
/home/USERNAME/railway/bootstrap/app.php
/home/USERNAME/railway/deploy/.env.host
```

### گام ۳ — قرار دادن `.env`

فایل `.env.host`:

```text
/home/USERNAME/railway/deploy/.env.host
```

را به ریشه پروژه منتقل و rename کن به:

```text
/home/USERNAME/railway/.env
```

یعنی کنار فایل `artisan` قرار بگیرد. محتوایش از قبل تنظیم شده است؛ نیازی به ویرایش نداری.

### گام ۴ — کپی فایلهای عمومی

**محتویات داخل پوشه:**

```text
/home/USERNAME/railway/deploy/public_html/
```

را کپی کن به Document Root زیردامنه:

```text
/home/USERNAME/public_html/crm/
```

یا اگر هاستت حالت دوم را دارد:

```text
/home/USERNAME/domains/crm.lemooshop.ir/
```

⚠️ دقت کن: **محتویات داخل `deploy/public_html` را کپی میکنی، نه خود پوشه `public_html` را.**

بعد از کپی، داخل Document Root باید اینها باشند:

```text
index.php
.htaccess
_setup.php
build/
images/
icon.svg
manifest.json
robots.txt
service-worker.js
```

و نباید چیزهایی مثل `app/`, `vendor/`, `storage/` داخل Document Root باشند.

### گام ۵ — دسترسیها

روی پوشههای داخل `/home/USERNAME/railway/` دسترسی بده:

```text
storage          → 755 یا 775
bootstrap/cache  → 755 یا 775
```

اگر پنل گزینهٔ «Writable» دارد، همانرا بزن.

### گام ۶ — اجرای نصاب

در مرورگر باز کن:

```text
https://crm.lemooshop.ir/_setup.php?token=a703d767de202e561258250e07233d0f
```

باید خروجی شبیه این ببینی:

```text
== migrate ==
...
status: 0

== db:seed ==
...
status: 0

DONE. The application is ready.
_setup.php was removed automatically.
```

اگر پیام `Laravel app root not found` دیدی، یعنی پوشه `railway` جای دیگری است؛ مسیر پوشههای `/home/USERNAME/` را در پنل چک کن و بگو تا اسکریپت را با مسیر دقیق تنظیم کنم.

### گام ۷ — حذف نصاب

پس از دیدن `DONE`، حتماً فایل زیر را حذف کن:

```text
Document Root/_setup.php
```

اسکریپت سعی میکند خودش را حذف کند، ولی اگر نشد، دستی حذفش کن.

### گام ۸ — ورود

```text
https://crm.lemooshop.ir/login

Email:    vahidmehdipour@gmail.com
Password: 12345678
```

پس از ورود، رمز عبور را عوض کن.

---

## ۵) عیبزدایی

| مشکل | علت |
|---|---|
| صفحه سفید / خطای 500 | فایل `storage/logs/laravel.log` را ببین |
| `Laravel app root not found` | پوشه `railway` یا `vendor` آپلود نشده |
| دوباره خطای 1071 | سه فایل اصلاحشده جایگزین نشدهاند: `AppServiceProvider.php`, migration مربوط به jobs, `DatabaseSeeder.php` |
| خطای اتصال به دیتابیس | `.env` سر جایش نیست یا یوزر/پسوورد اشتباه است |
| خطای `Class SettingSeeder not found` | فایل `database/seeders/DatabaseSeeder.php` آپلود نشده |
| موقع ZIP: `database.sqlite is being used by another process` | فایل SQLite لوکال را از ZIP خارج کن؛ نیازی به متوقف کردن سرویس هم نیست |

---

## ۶) خلاصه یک جملهای

**کل پروژه → `/home/USERNAME/railway/`**  
**محتویات `deploy/public_html` → Document Root زیردامنه**  
**`.env.host` → `/home/USERNAME/railway/.env`**  
**آدرس نصاب → `https://crm.lemooshop.ir/_setup.php?token=...`**
