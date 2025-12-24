@extends('layouts.app')
@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>My Tenders</h1>
    <a href="{{ route('tenders.create') }}" class="btn btn-primary">+ Create Tender</a>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Applications</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenders as $tender)
                <tr>
                    <td style="font-weight: 500;">{{ $tender->title }}</td>
                    <td>{{ $tender->deadline }}</td>
                    <td><span class="badge badge-open">{{ $tender->status }}</span></td>
                    <td>{{ $tender->applications->count() }}</td>
                    <td>
                        <a href="{{ route('tenders.show', $tender) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.75rem;">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No tenders created yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
