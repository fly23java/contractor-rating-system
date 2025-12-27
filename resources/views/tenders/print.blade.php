<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير تقييم المقاولين - {{ $tender->title }}</title>
    <style>
        :root {
            --primary: #4f46e5;
            --danger: #dc2626;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: var(--text);
            direction: rtl;
            background: white;
        }

        .no-print {
            display: none;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: var(--primary);
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border);
            margin-bottom: 30px;
        }

        .info-item strong {
            display: block;
            color: var(--text-muted);
            font-size: 12px;
            margin-bottom: 4px;
        }
        
        .section-title {
            background: var(--primary);
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 30px 0 15px 0;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid var(--border);
            padding: 8px;
            text-align: center;
        }
        
        th {
            background: #f1f5f9;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .rank-1 {
            background-color: #fef9c3 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .badge-pending { color: #f59e0b; background: #fef3c7; padding: 2px 6px; border-radius: 4px; }
        .badge-approved { color: #16a34a; background: #dcfce7; padding: 2px 6px; border-radius: 4px; }
        .badge-rejected { color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px; }

        @media print {
            body {
                padding: 0;
            }
            .header {
                margin-top: 0;
            }
            @page {
                margin: 1.5cm;
                size: A4 portrait;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير تقييم وتصنيف المقاولين</h1>
        <p class="subtitle">Contractor Ranking Report</p>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <strong>اسم المشروع</strong>
            {{ $tender->title }}
        </div>
        <div class="info-item">
            <strong>الميزانية</strong>
            ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}
        </div>
        <div class="info-item">
            <strong>تاريخ الإغلاق</strong>
            {{ $tender->deadline }}
        </div>
        <div class="info-item">
            <strong>تاريخ التقرير</strong>
            {{ now()->format('Y-m-d H:i') }}
        </div>
        <div class="info-item" style="grid-column: span 2;">
            <strong>الوصف</strong>
            {{ $tender->description }}
        </div>
    </div>

    <div class="section-title">🏆 الترتيب النهائي للمقاولين المقبولين</div>
    
    <table>
        <thead>
            <tr>
                <th width="50">#</th>
                <th>المقاول</th>
                <th>السعر المقدم</th>
                <th>النتيجة</th>
                <th>فني</th>
                <th>مالي</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accepted as $index => $app)
            <tr class="{{ $index === 0 ? 'rank-1' : '' }}">
                <td style="font-weight: bold;">{{ $index + 1 }}</td>
                <td style="text-align: right; font-weight: 500;">{{ $app->contractor->name }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td><strong>{{ number_format($app->weighted_total ?? 0, 2) }}%</strong></td>
                <td>{{ number_format($app->technical_score ?? 0, 2) }}%</td>
                <td>{{ number_format($app->financial_score ?? 0, 2) }}%</td>
                <td>
                    @if($app->status == 'PENDING') <span class="badge-pending">معلق</span> @endif
                    @if($app->status == 'APPROVED') <span class="badge-approved">مقبول</span> @endif
                    @if($app->status == 'REJECTED') <span class="badge-rejected">مرفوض</span> @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">لا توجد طلبات مقبولة</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($excluded->count() > 0)
    <div class="section-title" style="background: var(--danger);">❌ المقاولون المستبعدون</div>
    
    <table class="excluded-table">
        <thead>
            <tr>
                <th>المقاول</th>
                <th>السعر المقدم</th>
                <th>سبب الاستبعاد</th>
                <th>تاريخ الاستبعاد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($excluded as $app)
            <tr>
                <td style="text-align: right;">{{ $app->contractor->name }}</td>
                <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                <td style="color: var(--danger);">{{ $app->exclusion_reason }}</td>
                <td>{{ $app->excluded_at ? $app->excluded_at->format('Y-m-d H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer" style="margin-top: 50px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px; font-size: 11px; color: #666;">
        <p>تم إنشاء هذا التقرير تلقائياً بواسطة نظام تقييم وتصنيف المقاولين | {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
