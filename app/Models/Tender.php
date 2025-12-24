<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tender extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'title', 'description', 'min_price', 'max_price', 'deadline', 'status',
        'weight_price', 'weight_quality', 'weight_financial_capability', 'weight_experience',
        'weight_contract_terms', 'weight_field_experience', 'weight_executive_capability',
        'weight_post_service', 'weight_guarantees', 'weight_safety'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
