<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>گزارش کامل پرسنل</title>

    <style>
        @page {
            margin: 18px 20px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.6;
            direction: rtl;
            text-align: right;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header td {
            vertical-align: middle;
        }

        .brand {
            color: #4f46e5;
            font-size: 15px;
            font-weight: bold;
        }

        .subtitle,
        .meta {
            color: #64748b;
            font-size: 9px;
        }

        .meta {
            text-align: left;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 14px;
        }

        .summary {
            margin-bottom: 10px;
            color: #475569;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        th {
            background: #eef2ff;
            color: #3730a3;
            font-size: 9px;
            font-weight: bold;
            text-align: right;
        }

        th,
        td {
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .number {
            direction: ltr;
            text-align: center;
        }

        .score {
            color: #4338ca;
            font-weight: bold;
        }

        .badge {
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #e2e8f0;
            color: #475569;
        }

        .footer {
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="brand">{{ get_setting('app_name', 'سیستم امتیازدهی پرسنل راهآهن') }}</div>
                <div class="subtitle">راهآهن جمهوری اسلامی ایران</div>
            </td>
            <td class="meta">
                تاریخ گزارش: {{ jdate(now(), 'Y/m/d') }}<br>
                تعداد پرسنل: {{ fa_num($personnels->count()) }} نفر
            </td>
        </tr>
    </table>

    <h1>گزارش کامل پرسنل و امتیاز نهایی</h1>

    <div class="summary">
        @if ($filters['search'] !== '')
            جستجو: {{ $filters['search'] }} &nbsp;|
        @endif
        @if ($filters['role_id'] !== null)
            نقش: {{ fa_num($filters['role_id']) }} &nbsp;|
        @endif
        @if ($filters['status'] !== null)
            وضعیت: {{ $filters['status'] === 'active' ? 'فعال' : 'غیرفعال' }} &nbsp;|
        @endif
        مرتبسازی:
        @php
            $sortLabels = [
                'name' => 'نام',
                'score' => 'امتیاز نهایی',
                'entry_year' => 'سال ورود',
                'decree_year' => 'سال صدور حکم',
            ];
        @endphp
        {{ $sortLabels[$filters['sort']] }} ({{ $filters['direction'] === 'asc' ? 'صعودی' : 'نزولی' }})
    </div>

    @if ($personnels->isEmpty())
        <div style="padding: 30px; text-align: center; color: #64748b;">پرسنلی با مشخصات فوق یافت نشد.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 6%;">ردیف</th>
                    <th style="width: 20%;">نام و نام خانوادگی</th>
                    <th style="width: 12%;">نقش</th>
                    <th style="width: 9%;">سال ورود</th>
                    <th style="width: 9%;">سال حکم</th>
                    <th style="width: 10%;">امتیاز نهایی</th>
                    <th style="width: 10%;">کد ملی</th>
                    <th style="width: 9%;">وضعیت</th>
                    <th style="width: 15%;">ارزیابی‌ها</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personnels as $index => $personnel)
                    <tr>
                        <td class="number">{{ fa_num($index + 1) }}</td>
                        <td>{{ $personnel->full_name }}</td>
                        <td>{{ $personnel->role?->name_fa ?? '—' }}</td>
                        <td class="number">{{ fa_num($personnel->entry_year) }}</td>
                        <td class="number">{{ $personnel->decree_year ? fa_num($personnel->decree_year) : '—' }}</td>
                        <td class="number score">{{ fa_num(number_format((float) $personnel->computed_score, 2)) }}</td>
                        <td class="number">{{ fa_num($personnel->national_code) }}</td>
                        <td>
                            <span class="badge {{ $personnel->is_active ? 'active' : 'inactive' }}">
                                {{ $personnel->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="number">{{ fa_num($personnel->evaluations_count) }} مورد</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        {{ get_setting('footer_text', 'سیستم ارزیابی پرسنل راهآهن') }} — {{ get_setting('copyright_text', 'کلیه حقوق متعلق به راهآهن جمهوری اسلامی ایران میباشد.') }}
    </div>
</body>
</html>
