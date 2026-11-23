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
│       └── public_html/       ← توسط .cpanel.yml کپی میشود
│
└── rail.lemooshop.ir/         ← Document Root زیردامنه
    ├── index.php
    ├── .htaccess
    ├── build/
    └── _setup.php / _update.php
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
3. اسکریپت `_update.php` را اجرا کن
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

### اجرای نصب

باز کن:

```text
https://rail.lemooshop.ir/_setup.php?token=a703d767de202e561258250e07233d0f
```

اسکریپت قبل از اجرای migrate، مسیر ریشه و نام دیتابیس را چاپ میکند و اگر `DB_NAME` برابر `hwuxndqg_rail` نباشد، متوقف میشود. پس حتماً خروجی آن را ببین.

بعد از `DONE`، فایل `_setup.php` خودش حذف میشود.

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
4. باز کن:

```text
https://rail.lemooshop.ir/_update.php?token=a703d767de202e561258250e07233d0f
```

5. بعد از `UPDATE DONE`، `_update.php` حذف میشود.

⚠️ هرگز `.env` را overwrite نکن. `.cpanel.yml` فقط محتویات `deploy/public_html/` را کپی میکند، پس `.env` و `storage` دستنخورده میمانند.

---

## ۳) نکات امنیتی

- ریپو **Private** است و فقط از طریق Deploy Key قابل Clone است.
- `.env`، `.env.host`، `.env.rail` و فایلهای ZIP وارد ریپو نمیشوند.
- رمز دیتابیس را فقط در `.env` روی هاست نگه دار.
- Deploy Key فقط دسترسی خواندن دارد؛ نیازی به `Allow write access` نیست.
