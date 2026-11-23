# Changelog

All notable changes to this project are documented here. Versions follow
[Semantic Versioning](https://semver.org/) and each release has a matching Git tag.

## [1.2.0]

### Added

- Super-admin Excel personnel import wizard in Settings: CSV/XLSX/XML upload, pre-save preview, import with or without scores, CSV templates and a usage guide. Duplicate records (national code or full name) keep the existing profile and receive only the new scores.
- Activity log entries now show the actual before/after values of changed settings, stations and roles.
- Settings overview page with separate, icon-labelled tiles for scoring, stations, personnel roles and Excel import; each opens its own page.
- Searchable personnel dropdown at the top of the evaluation form's personnel list.
- Persian digits and RTL display across personnel, evaluation, dashboard, profile, PDF and log screens while values remain Latin in the database.
- Station list pagination with 3 (default), 10 and 20 items per page.

### Changed

- Personnel sorting now offers a primary and an optional secondary sort; evaluation sorting was removed.
- Print/PDF/Excel actions are compact icon-only buttons.
- Profile information and password forms are merged into one card with notification banners.

### Fixed

- Excel exports are UTF-8 with right-to-left reading order and correct Persian text.
- Evaluation live score preview and final-score cards display Persian digits.
- Clearing stale compiled Blade views no longer leaves a locked temporary file on Windows.

## [1.1.0]

### Added

- Application version shown below the logout button and under the login-page copyright.

### Changed

- Default color scheme is now light; a user's saved dark-mode choice is still respected.
- Railway safety terminology: «تصادف» → «سانحه» and «نزدیک تصادف» / «حادثه نزدیک» → «شبه سانحه».

## [1.0.0]

### Fixed

- Deployment scripts pinned to the dedicated `rail` app root and `hwuxndqg_rail` database.
- `.htaccess` is no longer overwritten on deploy, so the cPanel PHP 8.4 setting persists.
- Installer refuses to re-seed an already-installed database.
- Maintenance scripts are no longer shipped on every deploy; `_update.php` is restored only when missing.
