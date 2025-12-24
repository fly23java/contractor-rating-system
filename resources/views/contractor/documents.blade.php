@extends('layouts.app')
@section('content')
<h1>📎 إدارة المستندات المطلوبة</h1>
<p style="color: var(--text-muted); margin-bottom: 1rem;">قم برفع جميع المستندات المطلوبة للتأهيل. الصيغ المقبولة: PDF, JPG, PNG, DOC, DOCX (الحد الأقصى 10MB)</p>

<!-- Profile Verification Status -->
@if(auth()->user()->is_profile_verified)
    <div class="card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #16a34a; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: #16a34a; border-radius: 50%; display: grid; place-items: center; font-size: 2rem;">✓</div>
            <div style="flex: 1;">
                <h3 style="margin: 0; color: #166534;">ملفك الشخصي معتمد!</h3>
                <p style="margin: 0.5rem 0 0 0; color: #15803d;">يمكنك الآن التقديم على العطاءات المتاحة</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">عرض العطاءات</a>
        </div>
    </div>
@else
    <div class="card" style="background: linear-gradient(135deg, #fef9c3 0%, #fef08a 100%); border: 2px solid #eab308; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 60px; height: 60px; background: #eab308; border-radius: 50%; display: grid; place-items: center; font-size: 2rem;">⏳</div>
            <div style="flex: 1;">
                <h3 style="margin: 0; color: #854d0e;">ملفك الشخصي قيد المراجعة</h3>
                <p style="margin: 0.5rem 0 0 0; color: #a16207;">يجب رفع واعتماد جميع المستندات قبل التقديم على العطاءات</p>
            </div>
        </div>
    </div>
@endif

<!-- Progress Bar -->
@php
    $uploadedCount = $documents->count();
    $verifiedCount = $documents->where('is_verified', true)->count();
    $progress = ($uploadedCount / count($requiredDocuments)) * 100;
@endphp
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span style="font-weight: 600;">التقدم الإجمالي</span>
        <span style="font-weight: 600; color: var(--primary);">{{ round($progress) }}%</span>
    </div>
    <div style="background: #e2e8f0; height: 12px; border-radius: 999px; overflow: hidden;">
        <div style="background: linear-gradient(90deg, var(--primary), var(--accent)); height: 100%; width: {{ $progress }}%; transition: width 0.3s;"></div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; font-size: 0.875rem; color: var(--text-muted);">
        <span>{{ $uploadedCount }} / {{ count($requiredDocuments) }} مستند مرفوع</span>
        <span>{{ $verifiedCount }} مستند معتمد</span>
        <span>{{ count($requiredDocuments) - $uploadedCount }} مستند متبقي</span>
    </div>
</div>

<!-- Required Documents Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
    @foreach($requiredDocuments as $docType => $docLabel)
        @php
            $uploadedDoc = $documents->where('document_type', $docType)->first();
        @endphp
        
        <div class="card" style="padding: 1.5rem; {{ $uploadedDoc ? ($uploadedDoc->is_verified ? 'border: 2px solid #16a34a; background: #f0fdf4;' : 'border: 2px solid #eab308; background: #fefce8;') : 'border: 2px dashed #e2e8f0; background: #f8fafc;' }}">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <h4 style="margin: 0; font-size: 0.875rem; color: var(--text-main);">{{ $docLabel }}</h4>
                @if($uploadedDoc)
                    @if($uploadedDoc->is_verified)
                        <span style="background: #16a34a; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">✓ معتمد</span>
                    @else
                        <span style="background: #eab308; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">⏳ قيد المراجعة</span>
                    @endif
                @else
                    <span style="background: #dc2626; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: bold;">✗ مطلوب</span>
                @endif
            </div>

            @if($uploadedDoc)
                <!-- Uploaded Document Info -->
                <div style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">اسم الملف</div>
                    <div style="font-weight: 500; font-size: 0.875rem; margin-bottom: 0.75rem; word-break: break-all;">{{ $uploadedDoc->document_name }}</div>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted);">
                        <span>الحجم: {{ $uploadedDoc->file_size }} KB</span>
                        <span>{{ $uploadedDoc->created_at->format('Y-m-d') }}</span>
                    </div>

                    @if($uploadedDoc->verification_notes)
                        <div style="margin-top: 0.75rem; padding: 0.75rem; background: #fef9c3; border-radius: 6px; font-size: 0.75rem;">
                            <strong>ملاحظات المراجع:</strong> {{ $uploadedDoc->verification_notes }}
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('documents.download', $uploadedDoc) }}" class="btn btn-outline" style="flex: 1; padding: 0.5rem; font-size: 0.75rem;">
                        📥 تحميل
                    </a>
                    @if(!$uploadedDoc->is_verified)
                        <form action="{{ route('documents.delete', $uploadedDoc) }}" method="POST" style="flex: 1;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="width: 100%; padding: 0.5rem; font-size: 0.75rem; background: #fee2e2; color: #991b1b;" onclick="return confirm('هل تريد حذف هذا المستند؟')">
                                🗑️ حذف
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <!-- Upload Form -->
                <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="document_type" value="{{ $docType }}">
                    
                    <div class="form-group">
                        <label class="form-label" style="font-size: 0.75rem;">اختر الملف</label>
                        <input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.875rem;">
                        📤 رفع المستند
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>
@endsection
