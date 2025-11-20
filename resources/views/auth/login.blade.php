<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Student ID Registration Portal</title>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">
  @vite(['resources/css/app.css'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
@php
  $authError = $errors->first('identifier') ?? $errors->first('password');
  $statusMessage = session('status') ?? session('success');
  $statusHeading = session('success') ? 'Success' : 'Heads up';
@endphp
<body>
  <div class="auth-shell">
    <div class="absolute inset-x-0 top-0" aria-hidden="true">
      <div class="mx-auto h-32 w-32 rounded-full bg-emerald-100 blur-3xl opacity-60"></div>
    </div>

    <div class="auth-card">
      <section class="auth-illustration">
        <div>
          <div class="flex items-center gap-3 text-sm uppercase tracking-[0.3em] text-white/70">
            Cavite State University - Naic
          </div>
          <h2 class="mt-6">Student ID Registration Portal</h2>
          <p class="mt-4 text-base text-white/85">
            Manage your campus credentials in a secure, modern workspace inspired by CvSU's academic heritage.
          </p>
        </div>
        <ul class="space-y-4 text-sm">
          <li class="flex items-start gap-3">
            <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-white">&check;</span>
            Quick access to the ID application wizard.
          </li>
          <li class="flex items-start gap-3">
            <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-white">&check;</span>
            Secure review of requirements using CvSU authentication.
          </li>
          <li class="flex items-start gap-3">
            <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-white/20 text-white">&check;</span>
            Academic-grade privacy powered by modern tooling.
          </li>
        </ul>
        <div class="text-sm text-white/70">
          Need help? Email <a class="underline-offset-2 hover:underline" href="mailto:registrar.naic@cvsu.edu.ph">registrar.naic@cvsu.edu.ph</a>
        </div>
      </section>

      <section x-data="{ loading: false, showPassword: false }" class="space-y-6">
        <div class="space-y-2">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 font-semibold grid place-items-center">ID</div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600">CvSU Naic</p>
              <h1 class="text-2xl font-semibold text-neutral-900">Welcome back</h1>
            </div>
          </div>
          <p class="text-sm text-neutral-500">Enter your student number (or admin email) and password to continue.</p>
        </div>

        @if($authError)
          <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.opacity class="alert-danger">
            <div class="font-semibold">Invalid credentials</div>
            <p class="text-sm">{{ $authError }}</p>
          </div>
        @endif

        @if($statusMessage)
          <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.opacity class="alert-success">
            <div class="font-semibold">{{ $statusHeading }}</div>
            <p class="text-sm">{{ $statusMessage }}</p>
          </div>
        @endif

        <form x-ref="loginForm" method="POST" action="{{ url('/login') }}" class="space-y-5" @submit="loading = true">
          @csrf
          <div class="form-floating">
            <input id="identifier" name="identifier" type="text" inputmode="numeric" maxlength="64"
                   value="{{ old('identifier') }}" placeholder="Student number or email" required autofocus>
            <label for="identifier">Student Number or Admin Email</label>
            <p class="form-hint">Use your 9-digit student number (e.g., 202510123) or admin email.</p>
          </div>
          @error('identifier')<p class="form-error">{{ $message }}</p>@enderror

          <div class="form-floating" x-data="{ show: false }">
            <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
            <button type="button" class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-emerald-600"
                    @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'">
              <span x-text="show ? 'Hide' : 'Show'"></span>
            </button>
          </div>
          @error('password')<p class="form-error">{{ $message }}</p>@enderror

          <div class="flex flex-wrap items-center justify-between gap-3 text-sm text-neutral-600">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" class="h-4 w-4 rounded border-neutral-300 text-emerald-600 focus:ring-emerald-600" name="remember">
              Remember me
            </label>
            <a href="mailto:registrar.naic@cvsu.edu.ph?subject=Portal%20Password%20Assistance" class="font-semibold text-emerald-700 hover:text-emerald-600 focus-visible:outline focus-visible:outline-emerald-300 rounded">Forgot password?</a>
          </div>

          <div class="space-y-3">
            <button type="submit" class="btn-primary w-full" :disabled="loading">
              <span x-show="!loading">Log in</span>
              <span x-show="loading" class="flex items-center gap-2">
                <span class="btn-spinner"></span>
                Authenticating...
              </span>
            </button>
            <a href="{{ route('create-account') }}" class="btn-secondary w-full text-center">Create an account</a>
          </div>
        </form>
      </section>
    </div>
  </div>
</body>
</html>
