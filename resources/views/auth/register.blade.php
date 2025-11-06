<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — CvSU Naic Portal</title>
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
    .actions{margin-top:12px}
    .btn{display:inline-flex;justify-content:center;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;font-weight:700;border:1px solid transparent;cursor:pointer}
    .btn-primary{background:#f59e0b;color:#0f172a}
    .btn-primary:hover{background:#d97706}
    .muted{color:#64748b;font-size:13px;text-align:center;margin-top:14px}
    a{color:#065f46}
  </style>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  </head>
<body>
  <div class="card">
    <div class="hd">
      <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" alt="CvSU logo">
      <h1>Cavite State University Naic - Student Portal</h1>
      <p class="sub">Registration</p>
    </div>
    <div class="bd">
      <form method="POST" action="{{ url('/create-account') }}">
        @csrf
        <label class="form-label" for="student_number">Student Number</label>
        <input class="form-input-solid" id="student_number" name="student_number" inputmode="numeric" pattern="\d{9}" maxlength="9" value="{{ old('student_number') }}" placeholder="9-digit Student Number">
        @error('student_number')<p class="form-error">{{ $message }}</p>@enderror

        <div style="height:10px"></div>
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-input-solid" id="password" name="password" placeholder="Password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror

        <div style="height:10px"></div>
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input type="password" class="form-input-solid" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">

        <div class="actions">
          <button class="btn btn-primary" type="submit">Register</button>
        </div>
      </form>
      <p class="muted"><a href="{{ route('login') }}">Go back to login</a></p>
    </div>
  </div>
</body>
</html>
