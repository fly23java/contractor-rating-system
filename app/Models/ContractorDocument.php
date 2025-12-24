<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractorDocument extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Required document types
    const REQUIRED_DOCUMENTS = [
        'commercial_register' => 'السجل التجاري (Commercial Register)',
        'professional_license' => 'الرخصة المهنية (Professional License)',
        'tax_certificate' => 'شهادة التسجيل الضريبي (Tax Certificate)',
        'bank_statement' => 'كشف الحساب البنكي (Bank Statement)',
        'project_portfolio' => 'سجل المشاريع المنفذة (Project Portfolio)',
        'experience_certificates' => 'شهادات الخبرة (Experience Certificates)',
        'financial_statements' => 'القوائم المالية (Financial Statements)',
        'safety_plan' => 'خطة السلامة (HSE Plan)',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
