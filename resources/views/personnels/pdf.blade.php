<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>پروفایل پرسنل — {{ $personnel->full_name }}</title>

    <style>
        @page {
            margin: 24px 26px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1e293b;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.7;
            direction: rtl;
            text-align: right;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header td {
            vertical-align: middle;
        }

        .brand {
            color: #4f46e5;
            font-size: 16px;
            font-weight: bold;
        }

        .subtitle {
            color: #64748b;
            font-size: 10px;
        }

        .meta {
            color: #64748b;
            font-size: 10px;
            text-align: left;
        }

        .section-title {
            color: #0f172a;
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-table,
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            width: 50%;
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .info-label {
            display: block;
            color: #64748b;
            font-size: 9px;
        }

        .info-value {
            display: block;
            color: #0f172a;
            font-size: 11px;
            font-weight: bold;
        }

        .score-box {
            margin: 16px 0;
            padding: 14px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            text-align: center;
        }

        .score-label {
            color: #4338ca;
            font-size: 10px;
        }

        .score-value {
            color: #312e81;
            font-size: 28px;
            font-weight: bold;
            line-height: 1.3;
        }

        .score-hint {
            color: #6366f1;
            font-size: 9px;
        }

        .history-table {
            margin-top: 4px;
        }

        .history-table th {
            padding: 6px 5px;
            border: 1px solid #cbd5e1;
            background: #f1f5f9;
            color: #334155;
            font-size: 9px;
            text-align: right;
        }

        .history-table td {
            padding: 6px 5px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 9px;
            vertical-align: top;
        }

        .history-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .number {
            direction: rtl;
            display: inline-block;
        }

        .muted {
            color: #94a3b8;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 8px;
        }

        .badge-danger {
            background: #ffe4e6;
            color: #be123c;
        }

        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        .empty {
            padding: 24px;
            border: 1px dashed #cbd5e1;
            color: #94a3b8;
            text-align: center;
        }

        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $maxTotal = (float) get_setting('max_theory_score', 14)
            + (float) get_setting('max_practical_score', 4)
            + (float) get_setting('max_ethics_score', 2);
        $totalPenalties = $personnel->evaluations->sum(fn ($evaluation) => $evaluation->penaltyTotal());
    @endphp

    <table class="header">
        <tr>
            <td>
                <div class="brand">سیستم ارزیابی پرسنل راه‌آهن</div>
                <div class="subtitle">پروفایل پرسنل و تاریخچه ارزیابی‌ها</div>
            </td>
            <td class="meta">
                تاریخ گزارش: <span class="number">{{ jdate(now()) }}</span>
            </td>
        </tr>
    </table>

    <h2 class="section-title">اطلاعات پرسنل</h2>

    <table class="info-table">
        <tr>
            <td>
                <span class="info-label">نام و نام خانوادگی</span>
                <span class="info-value">{{ $personnel->full_name }}</span>
            </td>
            <td>
                <span class="info-label">نقش</span>
                <span class="info-value">{{ $personnel->role?->name_fa ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">سال ورود</span>
                <span class="info-value number">{{ fa_num($personnel->entry_year) }}</span>
            </td>
            <td>
                <span class="info-label">کد ملی</span>
                <span class="info-value number">{{ $personnel->national_code ? fa_num($personnel->national_code) : '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="info-label">وضعیت</span>
                <span class="info-value">{{ $personnel->is_active ? 'فعال' : 'غیرفعال' }}</span>
            </td>
            <td>
                <span class="info-label">تعداد ارزیابی‌ها</span>
                <span class="info-value number">{{ fa_num(number_format($personnel->evaluations->count())) }}</span>
            </td>
        </tr>
    </table>

    <div class="score-box">
        <div class="score-label">امتیاز نهایی پرسنل</div>
        <div class="score-value number">{{ fa_num(number_format($personnel->final_score, 2)) }}</div>
        <div class="score-hint">
            از حداکثر <span class="number">{{ fa_num(number_format($maxTotal, 2)) }}</span>
            — مجموع جریمه‌ها: <span class="number">−{{ fa_num(number_format($totalPenalties, 2)) }}</span>
        </div>
    </div>

    <h2 class="section-title">تاریخچه ارزیابی‌ها</h2>

    @if ($personnel->evaluations->isEmpty())
        <div class="empty">برای این پرسنل هنوز ارزیابی‌ای ثبت نشده است.</div>
    @else
        <table class="history-table">
            <thead>
                <tr>
                    <th style="width: 11%;">تاریخ (شمسی)</th>
                    <th style="width: 12%;">ایستگاه</th>
                    <th style="width: 8%;">تئوری</th>
                    <th style="width: 8%;">عملی</th>
                    <th style="width: 8%;">اخلاق</th>
                    <th style="width: 21%;">موارد منفی</th>
                    <th style="width: 9%;">امتیاز</th>
                    <th style="width: 23%;">توضیحات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($personnel->evaluations as $evaluation)
                    <tr>
                        <td class="number">{{ jdate($evaluation->evaluation_date) }}</td>
                        <td>{{ $evaluation->station?->name ?? '—' }}</td>
                        <td class="number">{{ $evaluation->theory_score !== null ? fa_num(number_format((float) $evaluation->theory_score, 2)) : '---' }}</td>
                        <td class="number">{{ $evaluation->practical_score !== null ? fa_num(number_format((float) $evaluation->practical_score, 2)) : '---' }}</td>
                        <td class="number">{{ $evaluation->ethics_score !== null ? fa_num(number_format((float) $evaluation->ethics_score, 2)) : '---' }}</td>
                        <td>
                            <span class="badge badge-danger">{{ fa_num($evaluation->accident_count) }} سانحه</span>
                            <span class="badge badge-warning">{{ fa_num($evaluation->near_miss_count) }} شبه سانحه</span>
                            <span class="badge badge-neutral">{{ fa_num($evaluation->warning_count) }} اخطار</span>
                            <br>
                            <span class="number">{{ fa_num($evaluation->penaltyTotal() > 0 ? '−'.number_format($evaluation->penaltyTotal(), 2) : '0.00') }}</span>
                        </td>
                        <td class="number" style="font-weight: bold;">{{ fa_num(number_format($evaluation->calculatedTotal(), 2)) }}</td>
                        <td>{{ $evaluation->description ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        این گزارش به صورت خودکار توسط سیستم ارزیابی پرسنل راه‌آهن تولید شده است.
    </div>
</body>
</html>
