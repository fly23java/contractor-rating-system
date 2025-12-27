@extends('layouts.app')
@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('dashboard') }}" style="color: var(--text-muted); text-decoration: none;">&larr; Back to Dashboard</a>
</div>

<div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
    <div>
        <h1 style="margin-bottom: 0.5rem;">{{ $tender->title }}</h1>
        <p style="color: var(--text-muted);">Deadline: {{ $tender->deadline }} | Budget: ${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}</p>
    </div>
    <div>
        <a href="{{ route('tenders.print-pdf', $tender) }}" target="_blank" class="btn btn-primary">🖨️ Print Report</a>
    </div>
</div>

<div class="card">
    <p>{{ $tender->description }}</p>
</div>

@php
    $accepted = $tender->applications->where('is_excluded', false)->sortByDesc('weighted_total');
    $excluded = $tender->applications->where('is_excluded', true);
@endphp

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: var(--primary);">{{ $tender->applications->count() }}</div>
        <div style="color: var(--text-muted); font-size: 0.875rem;">Total Applications</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: #16a34a;">{{ $accepted->count() }}</div>
        <div style="color: var(--text-muted); font-size: 0.875rem;">مقبول (Accepted)</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: #dc2626;">{{ $excluded->count() }}</div>
        <div style="color: var(--text-muted); font-size: 0.875rem;">مستبعد (Excluded)</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 2rem; font-weight: bold; color: #8b5cf6;">
            {{ $accepted->first() ? number_format($accepted->first()->weighted_total, 2) . '%' : '-' }}
        </div>
        <div style="color: var(--text-muted); font-size: 0.875rem;">Highest Score</div>
    </div>
</div>

<h2>🏆 Ranked Contractors (Accepted)</h2>
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 50px;">Rank</th>
                    <th>Contractor</th>
                    <th>Offer Price</th>
                    <th>Weighted Total</th>
                    <th>Technical Score</th>
                    <th>Financial Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accepted as $index => $app)
                <tr style="{{ $index === 0 ? 'background: #f0fdf4;' : '' }}">
                    <td>
                        @if($index === 0)
                            <span style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; padding: 0.25rem 0.5rem; border-radius: 8px; font-weight: bold;">🥇 #1</span>
                        @else
                            <span style="color: var(--text-muted);">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td style="font-weight: 500;">{{ $app->contractor->name }}</td>
                    <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                    <td>
                        <span style="font-weight: bold; color: var(--primary); font-size: 1.1rem;">
                            {{ number_format($app->weighted_total ?? 0, 2) }}%
                        </span>
                    </td>
                    <td>{{ number_format($app->technical_score ?? 0, 2) }}%</td>
                    <td>{{ number_format($app->financial_score ?? 0, 2) }}%</td>
                    <td>
                        @if($app->status == 'PENDING') <span class="badge badge-pending">Pending</span> @endif
                        @if($app->status == 'APPROVED') <span class="badge badge-approved">Approved</span> @endif
                        @if($app->status == 'REJECTED') <span class="badge badge-rejected">Rejected</span> @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No accepted applications yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($excluded->count() > 0)
<h2 style="margin-top: 3rem;">❌ Excluded Contractors</h2>
<div class="card" style="background: #fef2f2; border: 1px solid #fca5a5;">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Contractor</th>
                    <th>Offer Price</th>
                    <th>Exclusion Reason</th>
                    <th>Excluded At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($excluded as $app)
                <tr>
                    <td style="font-weight: 500;">{{ $app->contractor->name }}</td>
                    <td>${{ number_format($app->price_value ?? $app->price) }}</td>
                    <td style="color: #991b1b;">{{ $app->exclusion_reason }}</td>
                    <td>{{ $app->excluded_at ? $app->excluded_at->format('M d, Y H:i') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Detailed Scoring Breakdown (Expandable) -->
<details style="margin-top: 2rem;">
    <summary style="cursor: pointer; padding: 1rem; background: var(--surface); border-radius: var(--radius); box-shadow: var(--shadow); font-weight: 600; margin-bottom: 1rem;">
        📊 View Detailed Scoring Breakdown
    </summary>
    <div class="card" style="overflow-x: auto;">
        <h3>All Criteria Grades (1-5 Scale)</h3>
        <table class="table" style="font-size: 0.875rem;">
            <thead>
                <tr>
                    <th>Contractor</th>
                    <th>Price (11.44%)</th>
                    <th>Quality (11.26%)</th>
                    <th>Financial (11.20%)</th>
                    <th>Experience (11.14%)</th>
                    <th>Contract (11.03%)</th>
                    <th>Field Exp (10.97%)</th>
                    <th>Executive (10.73%)</th>
                    <th>Post-Service (9%)</th>
                    <th>Guarantees (8.56%)</th>
                    <th>Safety (7.67%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accepted as $app)
                <tr>
                    <td style="font-weight: 500;">{{ $app->contractor->name }}</td>
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
    </div>
</details>
@endsection
