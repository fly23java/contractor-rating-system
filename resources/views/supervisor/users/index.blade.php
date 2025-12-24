@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1>Manage Users</h1>
    <div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="margin-right: 1rem;">Back to Dashboard</a>
        <a href="{{ route('users.create') }}" class="btn btn-primary">➕ Add New User</a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role == 'contractor') <span class="badge badge-pending">Contractor</span>
                        @elseif($user->role == 'supervisor') <span class="badge badge-approved">Supervisor</span>
                        @elseif($user->role == 'owner') <span class="badge badge-rejected" style="background: #e0f2fe; color: #0369a1; border-color: #7dd3fc;">Owner</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
