<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log in — CvSU Naic Portal</title>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">
  @vite(['resources/css/app.css'])
  <style>
    body{min-height:100vh;margin:0;display:grid;place-items:center;
         background:linear-gradient(rgba(6,95,70,.93),rgba(6,95,70,.93)),
                    url('{{ asset('images/naic-campus.jpg') }}') center/cover no-repeat fixed;}
    .card{width:min(420px,92vw);background:#fff;border-radius:14px;box-shadow:0 15px 60px rgba(2,6,23,.25);border:1px solid #e2e8f0}
    .hd{padding:18px 20px 0;text-align:center}
    .hd img{height:44px}
    .hd h1{font-size:22px;margin:10px 0 0;color:#052e1a}
    .sub{color:#475569;font-size:14px;margin:12px 0 0}
    .bd{padding:18px 20px 22px}
    .actions{display:flex;gap:10px;margin-top:10px}
    .btn{display:inline-flex;justify-content:center;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-weight:700;border:1px solid transparent;cursor:pointer}
    .btn-primary{background:#047857;color:#fff}
    .btn-primary:hover{background:#065f46}
    .btn-ghost{background:#f8fafc;border-color:#e2e8f0;color:#0f172a}
    .muted{color:#64748b;font-size:13px;text-align:center;margin-top:14px}
    a{color:#065f46}
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  @if(session('success'))
    <script>setTimeout(()=>alert(@json(session('success'))),0)</script>
  @endif
  @if($errors->any())
    <script>setTimeout(()=>console.warn('Login errors:', @json($errors->all())),0)</script>
  @endif
  </head>
<body>
  <div class="card">
    <div class="hd">
      <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" alt="CvSU logo">
      <h1>Cavite State University Naic - Student Portal</h1>
      <p class="sub">Log in</p>
    </div>
    <div class="bd">
      <form method="POST" action="{{ url('/login') }}">
        @csrf
        <label class="form-label" for="identifier">Student Number (9 digits) or Admin Email</label>
        <input class="form-input-solid" id="identifier" name="identifier" inputmode="numeric" pattern="\d{9}|.*@.*" maxlength="64" value="{{ old('identifier') }}" placeholder="202110512 or admin email" autofocus>
        @error('identifier')<p class="form-error">{{ $message }}</p>@enderror

        <div style="height:10px"></div>
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-input-solid" id="password" name="password" placeholder="Password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror

        <div class="actions">
          <button class="btn btn-primary" type="submit">Login</button>
          <a class="btn btn-ghost" href="https://cvsu-naic.edu.ph" target="_blank" rel="noopener">Visit CvSU Naic Official Website</a>
        </div>
      </form>
      <p class="muted">
        Don’t have an account? <a href="{{ route('create-account') }}">Create your account</a>
      </p>
    </div>
  </div>
</body>
</html>
