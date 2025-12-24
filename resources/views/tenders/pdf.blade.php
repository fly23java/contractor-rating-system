<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير تقييم المقاولين</title>
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
        <h1>تقرير تقييم وتصنيف المقاولين</h1>
        <p style="color: #64748b; margin: 5px 0;">Contractor Ranking Report</p>
    </div>

    <div class="info-box">
        <p><strong>اسم المشروع:</strong> {{ $tender->title }}</p>
        <p><strong>الوصف:</strong> {{ $tender->description }}</p>
        <p><strong>الميزانية:</strong> ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}</p>
        <p><strong>تاريخ الإغلاق:</strong> {{ $tender->deadline }}</p>
        <p><strong>تاريخ التقرير:</strong> {{ now()->format('Y-m-d H:i') }}</p>
        <p><strong>عدد المتقدمين:</strong> {{ $accepted->count() + $excluded->count() }}</p>
    </div>

    <div class="section-title">🏆 الترتيب النهائي للمقاولين المقبولين</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">الترتيب</th>
                <th>اسم المقاول</th>
                <th>السعر المقدم</th>
                <th>النتيجة الإجمالية</th>
                <th>النقاط الفنية</th>
                <th>النقاط المالية</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accepted as $index => $app)
            <tr class="{{ $index === 0 ? 'rank-1' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $app->contractor->name }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td><strong>{{ number_format($app->weighted_total ?? 0, 2) }}%</strong></td>
                <td>{{ number_format($app->technical_score ?? 0, 2) }}%</td>
                <td>{{ number_format($app->financial_score ?? 0, 2) }}%</td>
                <td>{{ $app->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">لا توجد طلبات مقبولة</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($excluded->count() > 0)
    <div class="section-title" style="background: #dc2626;">❌ المقاولون المستبعدون</div>
    
    <table class="excluded-table">
        <thead>
            <tr>
                <th>اسم المقاول</th>
                <th>السعر المقدم</th>
                <th>سبب الاستبعاد</th>
                <th>تاريخ الاستبعاد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($excluded as $app)
            <tr>
                <td>{{ $app->contractor->name }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td>{{ $app->exclusion_reason }}</td>
                <td>{{ $app->excluded_at ? $app->excluded_at->format('Y-m-d H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="section-title">📊 التفاصيل الكاملة للمعايير العشرة</div>
    
    <table style="font-size: 10px;">
        <thead>
            <tr>
                <th>المقاول</th>
                <th>السعر<br>11.44%</th>
                <th>الجودة<br>11.26%</th>
                <th>القدرة المالية<br>11.20%</th>
                <th>الخبرة<br>11.14%</th>
                <th>الشروط<br>11.03%</th>
                <th>خبرة المجال<br>10.97%</th>
                <th>القدرة التنفيذية<br>10.73%</th>
                <th>الخدمات<br>9%</th>
                <th>الضمانات<br>8.56%</th>
                <th>السلامة<br>7.67%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accepted as $app)
            <tr>
                <td>{{ $app->contractor->name }}</td>
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
        <p>تم إنشاء هذا التقرير تلقائياً بواسطة نظام تقييم وتصنيف المقاولين</p>
        <p>Contractor Rating System - Automated Report Generation</p>
    </div>
</body>
</html>
