<?php

namespace App\Support;

final class Permissions
{
    public const VIEW_DASHBOARD = 'view_dashboard';

    public const VIEW_PERSONNELS = 'view_personnels';

    public const CREATE_PERSONNELS = 'create_personnels';

    public const EDIT_PERSONNELS = 'edit_personnels';

    public const DELETE_PERSONNELS = 'delete_personnels';

    public const VIEW_EVALUATIONS = 'view_evaluations';

    public const CREATE_EVALUATIONS = 'create_evaluations';

    public const EDIT_EVALUATIONS = 'edit_evaluations';

    public const DELETE_EVALUATIONS = 'delete_evaluations';

    public const VIEW_PERIODS = 'view_periods';

    public const CREATE_PERIODS = 'create_periods';

    public const EDIT_PERIODS = 'edit_periods';

    public const DELETE_PERIODS = 'delete_periods';

    public const MANAGE_SETTINGS = 'manage_settings';

    public const VIEW_ACTIVITY_LOG = 'view_activity_log';

    /** @var array<string, string> */
    public const LABELS = [
        self::VIEW_DASHBOARD => 'مشاهده داشبورد',
        self::VIEW_PERSONNELS => 'مشاهده پرسنل',
        self::CREATE_PERSONNELS => 'افزودن پرسنل',
        self::EDIT_PERSONNELS => 'ویرایش پرسنل',
        self::DELETE_PERSONNELS => 'حذف پرسنل',
        self::VIEW_EVALUATIONS => 'مشاهده ارزیابی‌ها',
        self::CREATE_EVALUATIONS => 'ثبت ارزیابی',
        self::EDIT_EVALUATIONS => 'ویرایش ارزیابی',
        self::DELETE_EVALUATIONS => 'حذف ارزیابی',
        self::VIEW_PERIODS => 'مشاهده دوره‌ها',
        self::CREATE_PERIODS => 'افزودن دوره',
        self::EDIT_PERIODS => 'ویرایش دوره',
        self::DELETE_PERIODS => 'حذف دوره',
        self::MANAGE_SETTINGS => 'تنظیمات سیستم',
        self::VIEW_ACTIVITY_LOG => 'مشاهده لاگ سیستم',
    ];

    /** @var array<string, string> */
    public const GROUPS = [
        self::VIEW_DASHBOARD => 'داشبورد',
        self::VIEW_PERSONNELS => 'پرسنل',
        self::CREATE_PERSONNELS => 'پرسنل',
        self::EDIT_PERSONNELS => 'پرسنل',
        self::DELETE_PERSONNELS => 'پرسنل',
        self::VIEW_EVALUATIONS => 'ارزیابی‌ها',
        self::CREATE_EVALUATIONS => 'ارزیابی‌ها',
        self::EDIT_EVALUATIONS => 'ارزیابی‌ها',
        self::DELETE_EVALUATIONS => 'ارزیابی‌ها',
        self::VIEW_PERIODS => 'دوره‌های ارزیابی',
        self::CREATE_PERIODS => 'دوره‌های ارزیابی',
        self::EDIT_PERIODS => 'دوره‌های ارزیابی',
        self::DELETE_PERIODS => 'دوره‌های ارزیابی',
        self::MANAGE_SETTINGS => 'امور مدیریتی',
        self::VIEW_ACTIVITY_LOG => 'امور مدیریتی',
    ];

    public static function labelFor(string $permission): string
    {
        return self::LABELS[$permission] ?? $permission;
    }

    public static function groupFor(string $permission): string
    {
        return self::GROUPS[$permission] ?? 'سایر';
    }

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::VIEW_DASHBOARD,
            self::VIEW_PERSONNELS,
            self::CREATE_PERSONNELS,
            self::EDIT_PERSONNELS,
            self::DELETE_PERSONNELS,
            self::VIEW_EVALUATIONS,
            self::CREATE_EVALUATIONS,
            self::EDIT_EVALUATIONS,
            self::DELETE_EVALUATIONS,
            self::VIEW_PERIODS,
            self::CREATE_PERIODS,
            self::EDIT_PERIODS,
            self::DELETE_PERIODS,
            self::MANAGE_SETTINGS,
            self::VIEW_ACTIVITY_LOG,
        ];
    }

    /** @return list<string> */
    public static function viewer(): array
    {
        return [
            self::VIEW_DASHBOARD,
            self::VIEW_PERSONNELS,
            self::VIEW_EVALUATIONS,
            self::VIEW_PERIODS,
        ];
    }

    /** @return list<string> */
    public static function editor(): array
    {
        return [
            self::CREATE_PERSONNELS,
            self::EDIT_PERSONNELS,
            self::CREATE_EVALUATIONS,
            self::EDIT_EVALUATIONS,
            self::CREATE_PERIODS,
            self::EDIT_PERIODS,
        ];
    }
}
