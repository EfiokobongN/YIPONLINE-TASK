@extends('layouts.app')
@section('title', 'Create Account')

@push('styles')
<style>
    .auth-wrapper { min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
    .auth-card { background: #fff; border-radius: var(--radius); box-shadow: var(--shadow); padding: 2.5rem; width: 100%; max-width: 580px; }
    .auth-logo { text-align: center; margin-bottom: 1.5rem; font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--brand); }
    .auth-logo span { color: var(--accent); }
    .auth-title { font-size: 1.3rem; font-weight: 700; text-align: center; margin-bottom: 1.5rem; }
    .auth-footer { text-align: center; margin-top: 1.2rem; font-size: .9rem; color: var(--muted); }
    .auth-footer a { color: var(--brand); font-weight: 600; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">YIP<span>Shop</span></div>
        <h2 class="auth-title">Create your account</h2>

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:1rem;max-width:none">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name"
                       class="form-control" value="{{ old('name') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email"
                       class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="text" name="phone" id="phone"
                       class="form-control" value="{{ old('phone') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="8">
                <p style="font-size:.8rem;color:var(--muted);margin-top:.3rem">Minimum 8 characters</p>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control" required>
            </div>
            <button type="submit" class="btn btn-accent btn-block" style="font-size:1rem;padding:.8rem">
                Create Account
            </button>
        </form>

        <p class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>
@endsection
