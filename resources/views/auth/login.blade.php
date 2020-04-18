@extends('layouts.mylayout')

<link rel="stylesheet" href="{{ asset('css/loginpagecss.css') }}">
@section('content')
<div class="login-container">
    <section class="login" id="login">
        <header>
            <h2>HomeHaven</h2>
            <h4>Login</h4>
        </header>

        <form class="login-form" action="{{ route('login') }}" method="POST">
            @csrf

            <input id="email" type="email" class="login-input @error('email') is-invalid @enderror" name="email" placeholder="User" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror

            <input id="password" type="password" class="login-input @error('password') is-invalid @enderror" placeholder="Password" name="password" required autocomplete="current-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="submit-container">
                <button type="submit" class="login-button">SIGN IN</button>
            </div>
        </form>
        <a style="font-size: 14px;" href="{{ route('password.request') }}">Don't remember Passowrd?</a>
    </section>
</div>
@endsection