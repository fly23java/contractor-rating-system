@extends('layouts.app')

@section('content')
<div class="card" style="width: 100%; max-width: 400px; padding: 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Welcome Back</h1>
        <p style="color: var(--text-muted);">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus placeholder="name@example.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>

        <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; font-size: 0.875rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); cursor: pointer;">
                <input type="checkbox"> Remember me
            </label>
            <a href="#" style="color: var(--primary); text-decoration: none;">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
    </form>
    
    <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.75rem; color: var(--text-muted); text-align: center;">
        <p>Demo Credentials:</p>
        <p><strong>Owner:</strong> owner@example.com / password</p>
        <p><strong>Contractor:</strong> contractor@example.com / password</p>
        <p><strong>Supervisor:</strong> supervisor@example.com / password</p>
    </div>
</div>
@endsection
