@extends('layouts.app')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Dashboard (Supervisor)</h1>
    <a href="{{ route('users.index') }}" class="btn btn-outline" style="background: white; border: 2px solid var(--primary); color: var(--primary);">👥 Manage Users</a>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>Pending Applications</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tender</th>
                    <th>Contractor</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td>{{ $app->tender->title }}</td>
                    <td>{{ $app->contractor->name }}</td>
                    <td>{{ $app->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if($app->status == 'PENDING') <span class="badge badge-pending">Waiting for Review</span>
                        @elseif($app->status == 'APPROVED') <span class="badge badge-approved">Approved</span>
                        @elseif($app->status == 'REJECTED') <span class="badge badge-rejected">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('applications.grade', $app) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                            {{ $app->status == 'PENDING' ? 'Grade & Verify' : 'Edit Grade' }}
                        </a>
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1rem;">No applications pending review.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3>Manage Tenders & Weights</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Owner</th>
                    <th>Deadline</th>
                    <th>Applications</th>
                    <th>Weights</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenders as $tender)
                <tr>
                    <td>{{ $tender->title }}</td>
                    <td>{{ $tender->owner->name }}</td>
                    <td>{{ $tender->deadline }}</td>
                    <td><span class="badge badge-pending">{{ $tender->applications_count }} Apps</span></td>
                    <td>
                        <a href="{{ route('tenders.weights.edit', $tender) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; width: fit-content;">
                            <span>⚙️</span> Customize Weights
                        </a>
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 1rem;">No tenders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
