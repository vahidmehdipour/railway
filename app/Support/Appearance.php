<?php

namespace App\Support;

final class Appearance
{
    public const APP_NAME = 'app_name';

    public const FOOTER_TEXT = 'footer_text';

    public const COPYRIGHT_TEXT = 'copyright_text';

    public const LOGIN_BACKGROUND = 'login_background';

    /** @var array<string, string> */
    public const DEFAULTS = [
        self::APP_NAME => 'سیستم امتیازدهی پرسنل راهآهن',
        self::FOOTER_TEXT => 'سیستم ارزیابی پرسنل راهآهن',
        self::COPYRIGHT_TEXT => 'کلیه حقوق متعلق به راهآهن جمهوری اسلامی ایران میباشد.',
        self::LOGIN_BACKGROUND => 'slate',
    ];

    /** @var array<string, string> */
    public const BACKGROUNDS = [
        'slate' => 'پیشفرض',
        'indigo' => 'بنفش',
        'emerald' => 'سبز',
        'sunset' => 'غروب',
    ];
}
