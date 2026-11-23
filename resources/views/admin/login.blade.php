@extends('layouts.auth', ['isAdmin' => true])

@section('title', 'Admin sign in')

@section('side')
    <span class="eyebrow"><x-icon name="shield" /> Control panel</span>
    <h2>Manage the library with confidence.</h2>
    <p>Upload books directly, review user requests, organise categories and manage members.</p>
@endsection

@section('content')
    <span class="chip" style="margin-bottom: 1rem;"><x-icon name="shield" /> Administrators only</span>
    <h1>Admin sign in</h1>
    <p>Use your administrator credentials.</p>

    <x-flash />

    <form action="{{ route('adminlogin.submit') }}" method="POST">
        @csrf
        <div class="field">
            <label for="email">Email address</label>
            <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="input" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg btn-block"><x-icon name="login" /> Sign in to dashboard</button>
    </form>

    <p class="auth-foot"><a href="{{ route('home') }}" class="muted"><x-icon name="arrow-left" /> Back to website</a></p>
@endsection
