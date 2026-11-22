# استقرار با cPanel Git™ Version Control

این روش جای ZIP دستی را میگیرد. نیازی به SSH یا Composer روی هاست نیست.

## ساختار روی هاست

```text
/home3/hwuxndqg/
├── railway-rail/              ← Repository Path (ریشه پروژه لاراول)
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

`index.php` سفارشی، پوشه `railway-rail` را بهعنوان ریشه پروژه پیدا میکند و مسیر public لاراول را روی Document Root تنظیم میکند.

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
/home3/hwuxndqg/railway-rail
```

5. Repository Name: مثلاً `railway-rail`.
6. Branch را `main` انتخاب کن.
7. بعد از clone، وارد **Manage → Pull or Deploy** شو:
   - **Update from Remote**
   - **Deploy HEAD Commit**

فایل `.cpanel.yml` برای تو کپی `deploy/public_html/.` به Document Root را انجام میدهد.

### ساخت `.env`

در File Manager داخل:

```text
/home3/hwuxndqg/railway-rail/.env
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
/home3/hwuxndqg/railway-rail/storage
/home3/hwuxndqg/railway-rail/bootstrap/cache
```

را قابل نوشتن ( writable) کن.

### اجرای نصب

باز کن:

```text
https://rail.lemooshop.ir/_setup.php?token=a703d767de202e561258250e07233d0f
```

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

- ریپو **Public** است، بنابراین `.env`، `.env.host`، `.env.rail` و فایلهای ZIP وارد آن نمیشوند.
- اگر روزی ریپو Private شد، میتوانی Deploy Key اضافه کنی؛ فعلاً لازم نیست.
- رمز دیتابیس را فقط در `.env` روی هاست نگه دار.
