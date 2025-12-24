@extends('layouts.app')
@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('dashboard') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
</div>
<h1>تقييم المقاول</h1>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Application Details -->
    <div class="card">
        <h3>Application Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <div class="form-label" style="color: var(--text-muted);">Tender</div>
                <div style="font-weight: 500; font-size: 1.1rem;">{{ $application->tender->title }}</div>
            </div>
            <div>
                <div class="form-label" style="color: var(--text-muted);">Contractor</div>
                <div style="font-weight: 500; font-size: 1.1rem;">{{ $application->contractor->name }}</div>
            </div>
            <div>
                <div class="form-label" style="color: var(--text-muted);">Applied On</div>
                <div>{{ $application->created_at->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="form-label" style="color: var(--text-muted);">Proposal Notes</div>
                <div>{{ $application->notes ?? 'No notes' }}</div>
            </div>
        </div>
    </div>

    <!-- Calculated Results (if graded before) -->
    @if($application->weighted_total)
    <div class="card" style="height: fit-content; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 2px solid #86efac;">
        <h3 style="color: #166534;">النتيجة النهائية</h3>
        <div style="font-size: 2rem; font-weight: bold; color: #16a34a; margin: 1rem 0;">
            {{ number_format($application->weighted_total, 2) }}%
        </div>
        <div style="display: grid; gap: 0.5rem; font-size: 0.875rem;">
            <div style="display: flex; justify-content: space-between;">
                <span>Technical Score:</span>
                <strong>{{ number_format($application->technical_score, 2) }}%</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Financial Score:</span>
                <strong>{{ number_format($application->financial_score, 2) }}%</strong>
            </div>
        </div>
        @if($application->is_excluded)
            <div style="margin-top: 1rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; color: #991b1b;">
                <strong>مستبعد:</strong> {{ $application->exclusion_reason }}
            </div>
        @endif
    </div>
    @endif
</div>

<!-- Uploaded Documents Review -->
<div class="card" style="margin-top: 2rem;">
    <h3>📎 Uploaded Documents</h3>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Document Type</th>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($application->documents as $doc)
                <tr>
                    <td>{{ \App\Models\ContractorDocument::REQUIRED_DOCUMENTS[$doc->document_type] ?? $doc->document_type }}</td>
                    <td>{{ $doc->document_name }}</td>
                    <td>{{ $doc->file_size }} KB</td>
                    <td>
                        @if($doc->is_verified)
                            <span class="badge badge-approved">✓ Verified</span>
                        @else
                            <span class="badge badge-pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('documents.download', $doc) }}" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; margin-right: 0.5rem;">Download</a>
                        
                        @if(!$doc->is_verified)
                            <button onclick="document.getElementById('verify-{{$doc->id}}').showModal()" class="btn btn-primary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Verify</button>
                            
                            <dialog id="verify-{{$doc->id}}" style="padding: 0; border-radius: 12px; border: none; width: 400px; box-shadow: var(--shadow-lg);">
                                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                                    <h3 style="margin: 0;">Verify Document</h3>
                                </div>
                                <form action="{{ route('documents.verify', $doc) }}" method="POST" style="padding: 1.5rem;">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Status</label>
                                        <select name="is_verified" class="form-control" required>
                                            <option value="1">✓ Verified</option>
                                            <option value="0">✗ Rejected</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Notes</label>
                                        <textarea name="verification_notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                        <button type="submit" class="btn btn-primary" style="flex: 1;">Save</button>
                                        <button type="button" onclick="document.getElementById('verify-{{$doc->id}}').close()" class="btn btn-outline" style="flex: 1;">Cancel</button>
                                    </div>
                                </form>
                            </dialog>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1rem;">No documents uploaded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Grading Form -->
<form action="{{ route('applications.update', $application) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin-top: 2rem;">
        <!-- Financial Criteria -->
        <div class="card">
            <h3 style="color: var(--primary); border-bottom: 2px solid var(--primary); padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
                معايير مالية (Financial Criteria)
            </h3>
            
            <div class="form-group">
                <label class="form-label">1. السعر (Price) - 11.44%</label>
                <input type="number" name="price_value" class="form-control" step="0.01" value="{{ old('price_value', $application->price_value) }}" required>
                <small style="color: var(--text-muted);">القيمة المالية الفعلية (مثال: 120000)</small>
            </div>

            <div class="form-group">
                <label class="form-label">3. القدرة المالية (Financial Capability) - 11.20%</label>
                <input type="number" name="financial_capability" class="form-control" step="0.01" value="{{ old('financial_capability', $application->financial_capability) }}" required>
                <small style="color: var(--text-muted);">حجم الميزانية أو السيولة</small>
            </div>

            <div class="form-group">
                <label class="form-label">9. الضمانات (Guarantees) - 8.56%</label>
                <input type="number" name="guarantees_value" class="form-control" step="0.01" value="{{ old('guarantees_value', $application->guarantees_value) }}" required>
                <small style="color: var(--text-muted);">قيمة الضمانات المقدمة</small>
            </div>
        </div>

        <!-- Technical Criteria -->
        <div class="card">
            <h3 style="color: #8b5cf6; border-bottom: 2px solid #8b5cf6; padding-bottom: 0.5rem; margin-bottom: 1.5rem;">
                معايير فنية (Technical Criteria)
            </h3>

            <div class="form-group">
                <label class="form-label">2. الجودة (Quality) - 11.26%</label>
                <input type="number" name="quality_points" class="form-control" min="0" max="100" step="0.01" value="{{ old('quality_points', $application->quality_points) }}" required>
                <small style="color: var(--text-muted);">نقاط تقييم الجودة (0-100)</small>
            </div>

            <div class="form-group">
                <label class="form-label">4. الخبرة (Experience) - 11.14%</label>
                <input type="number" name="experience_projects" class="form-control" min="0" value="{{ old('experience_projects', $application->experience_projects) }}" required>
                <small style="color: var(--text-muted);">عدد المشاريع المنفذة</small>
            </div>

            <div class="form-group">
                <label class="form-label">5. الشروط التعاقدية (Contract Terms) - 11.03%</label>
                <input type="number" name="contract_terms_points" class="form-control" min="0" max="100" step="0.01" value="{{ old('contract_terms_points', $application->contract_terms_points) }}" required>
                <small style="color: var(--text-muted);">نقاط التوافق مع شروط العقد (0-100)</small>
            </div>

            <div class="form-group">
                <label class="form-label">6. الخبرة في المجال (Field Experience) - 10.97%</label>
                <input type="number" name="field_experience_projects" class="form-control" min="0" max="5" value="{{ old('field_experience_projects', $application->field_experience_projects) }}" required>
                <small style="color: var(--text-muted);">عدد المشاريع في نفس التخصص (0-5)</small>
            </div>

            <div class="form-group">
                <label class="form-label">7. القدرة التنفيذية (Executive Capability) - 10.73%</label>
                <input type="number" name="executive_capability_points" class="form-control" min="0" max="100" step="0.01" value="{{ old('executive_capability_points', $application->executive_capability_points) }}" required>
                <small style="color: var(--text-muted);">نقاط تقييم المعدات والكادر (0-100)</small>
            </div>

            <div class="form-group">
                <label class="form-label">8. الالتزام بالخدمات اللاحقة (Post-Service) - 9.00%</label>
                <input type="number" name="post_service_months" class="form-control" min="0" max="24" value="{{ old('post_service_months', $application->post_service_months) }}" required>
                <small style="color: var(--text-muted);">فترة الالتزام بالصيانة (بالأشهر، الحد الأقصى 24)</small>
            </div>

            <div class="form-group">
                <label class="form-label">10. السلامة والأمان (Safety) - 7.67%</label>
                <input type="number" name="safety_points" class="form-control" min="50" max="95" step="0.01" value="{{ old('safety_points', $application->safety_points) }}" required>
                <small style="color: var(--text-muted);">نقاط نظام السلامة (50-95)</small>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="form-group">
            <label class="form-label">حالة التقييم (Status)</label>
            <select name="status" class="form-control">
                <option value="PENDING" @selected($application->status == 'PENDING')>Pending</option>
                <option value="APPROVED" @selected($application->status == 'APPROVED')>Approved</option>
                <option value="REJECTED" @selected($application->status == 'REJECTED')>Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
            💾 حفظ وحساب النقاط تلقائياً
        </button>
        <p style="text-align: center; color: var(--text-muted); margin-top: 1rem; font-size: 0.875rem;">
            سيتم حساب الدرجات (1-5) والنتيجة المرجحة تلقائياً بعد الحفظ
        </p>
    </div>
</form>
@endsection
