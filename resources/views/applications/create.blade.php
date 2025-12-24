@extends('layouts.app')
@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="display: inline-flex; align-items: center; gap: 0.5rem;">
            ← Back to Tenders
        </a>
    </div>

    <h1>📋 التقديم على العطاء</h1>
    <div class="card" style="margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid var(--primary);">
        <h2 style="margin: 0 0 0.5rem 0; color: var(--primary);">{{ $tender->title }}</h2>
        <p style="margin: 0; color: var(--text-muted);">{{ $tender->description }}</p>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <div>
                <strong>الميزانية:</strong> ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}
            </div>
            <div>
                <strong>آخر موعد:</strong> {{ $tender->deadline }}
            </div>
        </div>
    </div>

    <form action="{{ route('applications.store', $tender) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Basic Information -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: var(--primary); display: flex; align-items: center; gap: 0.5rem;">
                💼 عرض الأسعار والبيانات المالية
            </h3>
            
            <div class="form-group">
                <label class="form-label">السعر المقدم (Offer Price) *</label>
                <input type="number" name="price" class="form-control" step="0.01" min="0" required value="{{ old('price') }}">
                <small style="color: var(--text-muted);">الميزانية المحددة: ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}</small>
            </div>

            <div class="form-group">
                <label class="form-label">القدرة المالية / السيولة (Financial Capability) *</label>
                <input type="number" name="financial_capability" class="form-control" step="0.01" min="0" required value="{{ old('financial_capability') }}">
                <small style="color: var(--text-muted);">قيمة السيولة النقدية أو خط الائتمان المتوفر للمشروع</small>
            </div>

            <div class="form-group">
                <label class="form-label">قيمة الضمانات المقدمة (Guarantees) *</label>
                <input type="number" name="guarantees_value" class="form-control" step="0.01" min="0" required value="{{ old('guarantees_value') }}">
                <small style="color: var(--text-muted);">قيمة الضمان البنكي أو الضمانات الأخرى</small>
            </div>
        </div>

        <!-- Technical Information -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <h3 style="margin: 0 0 1.5rem 0; color: #8b5cf6; display: flex; align-items: center; gap: 0.5rem;">
                🛠️ الخبرات والبيانات الفنية
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">عدد المشاريع السابقة (Total Projects) *</label>
                    <input type="number" name="experience_projects" class="form-control" min="0" required value="{{ old('experience_projects') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">مشاريع في نفس المجال (Field Projects) *</label>
                    <input type="number" name="field_experience_projects" class="form-control" min="0" max="5" required value="{{ old('field_experience_projects') }}">
                    <small style="color: var(--text-muted);">الحد الأقصى: 5 مشاريع</small>
                </div>

                <div class="form-group">
                    <label class="form-label">مدة الصيانة (Post-Service Months) *</label>
                    <input type="number" name="post_service_months" class="form-control" min="0" max="24" required value="{{ old('post_service_months') }}">
                    <small style="color: var(--text-muted);">عدد الأشهر (0 - 24 شهر)</small>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">ملاحظات / تفاصيل العرض الفني (Technical Proposal Details)</label>
                <textarea name="notes" class="form-control" rows="6" placeholder="تفاصيل إضافية حول خطة العمل، الجدول الزمني، الكادر الفني، وأي معلومات تدعم تقييم الجودة والسلامة...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Document Upload Section -->
        <div class="card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: #8b5cf6; display: flex; align-items: center; gap: 0.5rem;">
                    📎 المستندات المطلوبة
                </h3>
                <span style="padding: 0.25rem 0.75rem; background: #fef9c3; color: #854d0e; border-radius: 999px; font-size: 0.75rem; font-weight: 600;"></span>
            </div>

            <div style="background: #fffbeb; border: 1px solid #fbbf24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <div style="display: flex; gap: 0.5rem; align-items: start;">
                    <span style="font-size: 1.25rem;">⚠️</span>
                    <div style="font-size: 0.9rem; color: #92400e;">
                        <strong>تنبيه هام جداً:</strong> يجب عليك إرفاق مستند يثبت صحة كل رقم أو معلومة تقوم بإدخالها. أي معلومة بدون إثبات سيتم تجاهلها أو استبعاد العطاء بالكامل.
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                @foreach(\App\Models\ContractorDocument::REQUIRED_DOCUMENTS as $docType => $docLabel)
                <div class="form-group" style="margin: 0;">
                    <label class="form-label" style="font-weight: 500;">{{ $docLabel }}</label>
                    <input type="file" name="documents[{{ $docType }}]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>
                @endforeach

                <!-- New Multi-File Upload -->
                <div class="form-group" style="margin: 0; grid-column: span 2;">
                    <label class="form-label" style="font-weight: 500; color: var(--primary);">📂 مستندات داعمة إضافية (Group of Files)</label>
                    <input type="file" name="documents[supporting_documents][]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <small style="color: var(--text-muted);">يمكنك تحديد ملفات متعددة في وقت واحد (إثباتات المشاريع، الكشوفات، الخ...)</small>
                </div>
            </div>

            <div style="margin-top: 1.5rem; padding: 0.75rem; background: #f8fafc; border-radius: 6px; font-size: 0.75rem; color: var(--text-muted); border: 1px dashed #cbd5e1;">
                📄 <strong>الصيغ المقبولة:</strong> PDF, JPG, PNG, DOC, DOCX • <strong>الحد الأقصى:</strong> 10MB لكل ملف
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 2; padding: 1rem; font-size: 1.1rem; font-weight: 600;">
                ✅ Submit Application
            </button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline" style="flex: 1; padding: 1rem; text-align: center; font-size: 1.1rem;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
