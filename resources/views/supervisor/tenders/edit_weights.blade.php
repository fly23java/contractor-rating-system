@extends('layouts.app')
@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('dashboard') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
</div>

<h1>📋 إدارة أوزان العطاء (Manage Tender Weights)</h1>
<div class="card" style="margin-bottom: 2rem; background: #f8fafc;">
    <h2 style="margin: 0 0 0.5rem 0;">{{ $tender->title }}</h2>
    <p style="margin: 0; color: var(--text-muted);">{{ $tender->description }}</p>
</div>

<form action="{{ route('tenders.weights.update', $tender) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h3 style="margin: 0;">تحديد الأوزان (Criteria Weights)</h3>
                <p style="color: var(--text-muted); margin: 0.5rem 0 0 0; font-size: 0.875rem;">
                    حدد النسب المئوية لكل معيار (المجموع يجب أن يساوي 100%)
                </p>
            </div>
            <button type="button" onclick="setDefaultWeights()" class="btn btn-outline" style="font-size: 0.875rem;">
                استعادة القيم الافتراضية
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            <!-- Financial Criteria -->
            <div style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border);">
                <h4 style="margin-top: 0; color: var(--primary);">المعايير المالية</h4>
                
                <div class="form-group">
                    <label class="form-label">1. السعر (Price)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_price" id="weight_price" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_price', $tender->weight_price) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">3. القدرة المالية (Financial Capability)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_financial_capability" id="weight_financial_capability" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_financial_capability', $tender->weight_financial_capability) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">9. الضمانات (Guarantees)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_guarantees" id="weight_guarantees" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_guarantees', $tender->weight_guarantees) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>
            </div>

            <!-- Technical Criteria -->
            <div style="background: white; padding: 1rem; border-radius: 8px; border: 1px solid var(--border);">
                <h4 style="margin-top: 0; color: #8b5cf6;">المعايير الفنية</h4>

                <div class="form-group">
                    <label class="form-label">2. الجودة (Quality)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_quality" id="weight_quality" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_quality', $tender->weight_quality) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">4. الخبرة (Experience)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_experience" id="weight_experience" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_experience', $tender->weight_experience) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">5. الشروط التعاقدية (Contract Terms)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_contract_terms" id="weight_contract_terms" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_contract_terms', $tender->weight_contract_terms) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">6. الخبرة في المجال (Field Experience)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_field_experience" id="weight_field_experience" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_field_experience', $tender->weight_field_experience) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">7. القدرة التنفيذية (Executive Capability)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_executive_capability" id="weight_executive_capability" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_executive_capability', $tender->weight_executive_capability) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">8. التزام المقاول بالخدمات اللاحقة (Contractor Commitment to Post-Service)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_post_service" id="weight_post_service" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_post_service', $tender->weight_post_service) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">10. السلامة والأمان (Safety)</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" name="weight_safety" id="weight_safety" class="form-control" step="0.01" min="0" max="100" value="{{ old('weight_safety', $tender->weight_safety) }}" required>
                        <span style="color: var(--text-muted);">%</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="total-warning" style="margin-top: 1rem; padding: 1rem; background: #fef9c3; border-radius: 8px; display: none;">
            <strong style="color: #854d0e;">⚠️ تحذير:</strong>
            <span id="total-message" style="color: #a16207;"></span>
        </div>

        <div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px; text-align: center;">
            <strong>المجموع الحالي:</strong> <span id="total-weight" style="font-size: 1.5rem; font-weight: bold; color: var(--primary);">100.00</span>%
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-top: 2rem;">
        💾 حفظ تحديثات الأوزان
    </button>
</form>

<script>
function setDefaultWeights() {
    document.getElementById('weight_price').value = 11.16;
    document.getElementById('weight_quality').value = 10.90;
    document.getElementById('weight_financial_capability').value = 10.90;
    document.getElementById('weight_experience').value = 10.87;
    document.getElementById('weight_contract_terms').value = 10.75;
    document.getElementById('weight_field_experience').value = 10.61;
    document.getElementById('weight_executive_capability').value = 10.47;
    document.getElementById('weight_post_service').value = 8.60;
    document.getElementById('weight_guarantees').value = 8.32;
    document.getElementById('weight_safety').value = 7.42;
    calculateTotal();
}

function calculateTotal() {
    const weights = [
        'weight_price', 'weight_quality', 'weight_financial_capability', 'weight_experience',
        'weight_contract_terms', 'weight_field_experience', 'weight_executive_capability',
        'weight_post_service', 'weight_guarantees', 'weight_safety'
    ];
    
    let total = 0;
    weights.forEach(id => {
        const value = parseFloat(document.getElementById(id).value) || 0;
        total += value;
    });
    
    document.getElementById('total-weight').textContent = total.toFixed(2);
    
    const warning = document.getElementById('total-warning');
    const message = document.getElementById('total-message');
    
    if (Math.abs(total - 100) > 0.01) {
        warning.style.display = 'block';
        message.textContent = `المجموع يجب أن يساوي 100% (الحالي: ${total.toFixed(2)}%)`;
    } else {
        warning.style.display = 'none';
    }
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[name^="weight_"]');
    inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
    calculateTotal();
});
</script>
@endsection
