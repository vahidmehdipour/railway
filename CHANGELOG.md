# Changelog

All notable changes to this project are documented here. Versions follow
[Semantic Versioning](https://semver.org/) and each release has a matching Git tag.

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
