@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('users.index') }}" class="btn btn-outline">← Back to Users</a>
    </div>

    <h1>Add New User</h1>

    <div class="card">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="contractor">Contractor (المقاول)</option>
                    <option value="owner">Owner (المالك)</option>
                    <option value="supervisor">Supervisor (المشرف)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 1rem; font-size: 1.1rem;">Create User</button>
        </form>
    </div>
</div>
@endsection
