<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ \App\Helpers\ArabicHtml::reshape('تقرير تقييم المقاولين') }}</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 24px;
        }
        .info-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }
        .info-box p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #4f46e5;
            color: white;
            font-weight: bold;
        }
        .rank-1 {
            background: #fef9c3;
            font-weight: bold;
        }
        .excluded-table th {
            background: #dc2626;
        }
        .excluded-table {
            background: #fef2f2;
        }
        .section-title {
            background: #4f46e5;
            color: white;
            padding: 10px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #64748b;
            font-size: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ \App\Helpers\ArabicHtml::reshape('تقرير تقييم وتصنيف المقاولين') }}</h1>
        <p style="color: #64748b; margin: 5px 0;">Contractor Ranking Report</p>
    </div>

    <div class="info-box">
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('اسم المشروع:') }}</strong> {{ \App\Helpers\ArabicHtml::reshape($tender->title) }}</p>
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('الوصف:') }}</strong> {{ \App\Helpers\ArabicHtml::reshape($tender->description) }}</p>
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('الميزانية:') }}</strong> ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}</p>
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('تاريخ الإغلاق:') }}</strong> {{ $tender->deadline }}</p>
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('تاريخ التقرير:') }}</strong> {{ now()->format('Y-m-d H:i') }}</p>
        <p><strong>{{ \App\Helpers\ArabicHtml::reshape('عدد المتقدمين:') }}</strong> {{ $accepted->count() + $excluded->count() }}</p>
    </div>

    <div class="section-title">{{ \App\Helpers\ArabicHtml::reshape('🏆 الترتيب النهائي للمقاولين المقبولين') }}</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">{{ \App\Helpers\ArabicHtml::reshape('الترتيب') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('اسم المقاول') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('السعر المقدم') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('النتيجة الإجمالية') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('النقاط الفنية') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('النقاط المالية') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الحالة') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accepted as $index => $app)
            <tr class="{{ $index === 0 ? 'rank-1' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ \App\Helpers\ArabicHtml::reshape($app->contractor->name) }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td><strong>{{ number_format($app->weighted_total ?? 0, 2) }}%</strong></td>
                <td>{{ number_format($app->technical_score ?? 0, 2) }}%</td>
                <td>{{ number_format($app->financial_score ?? 0, 2) }}%</td>
                <td>{{ \App\Helpers\ArabicHtml::reshape($app->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">{{ \App\Helpers\ArabicHtml::reshape('لا توجد طلبات مقبولة') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($excluded->count() > 0)
    <div class="section-title" style="background: #dc2626;">{{ \App\Helpers\ArabicHtml::reshape('❌ المقاولون المستبعدون') }}</div>
    
    <table class="excluded-table">
        <thead>
            <tr>
                <th>{{ \App\Helpers\ArabicHtml::reshape('اسم المقاول') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('السعر المقدم') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('سبب الاستبعاد') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('تاريخ الاستبعاد') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($excluded as $app)
            <tr>
                <td>{{ \App\Helpers\ArabicHtml::reshape($app->contractor->name) }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td>{{ \App\Helpers\ArabicHtml::reshape($app->exclusion_reason) }}</td>
                <td>{{ $app->excluded_at ? $app->excluded_at->format('Y-m-d H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="section-title">{{ \App\Helpers\ArabicHtml::reshape('📊 التفاصيل الكاملة للمعايير العشرة') }}</div>
    
    <table style="font-size: 10px;">
        <thead>
            <tr>
                <th>{{ \App\Helpers\ArabicHtml::reshape('المقاول') }}</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('السعر') }}<br>11.44%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الجودة') }}<br>11.26%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('القدرة المالية') }}<br>11.20%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الخبرة') }}<br>11.14%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الشروط') }}<br>11.03%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('خبرة المجال') }}<br>10.97%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('القدرة التنفيذية') }}<br>10.73%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الخدمات') }}<br>9%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('الضمانات') }}<br>8.56%</th>
                <th>{{ \App\Helpers\ArabicHtml::reshape('السلامة') }}<br>7.67%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accepted as $app)
            <tr>
                <td>{{ \App\Helpers\ArabicHtml::reshape($app->contractor->name) }}</td>
                <td>{{ number_format($app->price_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->quality_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->financial_capability_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->experience_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->contract_terms_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->field_experience_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->executive_capability_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->post_service_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->guarantees_grade ?? 0, 2) }}</td>
                <td>{{ number_format($app->safety_grade ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ \App\Helpers\ArabicHtml::reshape('تم إنشاء هذا التقرير تلقائياً بواسطة نظام تقييم وتصنيف المقاولين') }}</p>
        <p>Contractor Rating System - Automated Report Generation</p>
    </div>
</body>
</html>
