@extends('layouts.app')
@section('content')
<h1>Open Tenders</h1>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Budget</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenders as $tender)
                <tr>
                    <td style="font-weight: 500;">{{ $tender->title }}</td>
                    <td>{{ $tender->deadline }}</td>
                    <td>${{ number_format($tender->min_price) }} - ${{ number_format($tender->max_price) }}</td>
                    <td>
                        @if($myApplications->contains('tender_id', $tender->id))
                            <span class="badge badge-pending">Applied</span>
                        @else
                            <a href="{{ route('applications.create', $tender) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">Apply</a>
                        @endif
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">No open tenders available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h2>My Applications</h2>
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tender</th>
                    <th>My Price</th>
                    <th>Status</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myApplications as $app)
                <tr>
                    <td style="font-weight: 500;">{{ $app->tender->title }}</td>
                    <td>${{ number_format($app->price) }}</td>
                    <td>
                        @if($app->status == 'PENDING') <span class="badge badge-pending">Pending</span> @endif
                        @if($app->status == 'APPROVED') <span class="badge badge-approved">Approved</span> @endif
                        @if($app->status == 'REJECTED') <span class="badge badge-rejected">Rejected</span> @endif
                    </td>
                    <td>
                        @if($app->score) {{ $app->score }}/100 @else - @endif
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1rem;">You haven't applied to any tenders yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
