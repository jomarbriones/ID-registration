<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student ID Registration Portal</title>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">
  <style>
    :root{--emerald:#065f46;--gold:#f59e0b}
    *{box-sizing:border-box}
    body{min-height:100vh;margin:0;display:grid;place-items:center; font-family:Inter,system-ui,Segoe UI,Roboto,Arial;
         background:linear-gradient(rgba(6,95,70,.93),rgba(6,95,70,.93)),
                    url('{{ asset('images/naic-campus.jpg') }}') center/cover no-repeat fixed;}
    .card{width:min(860px,96vw);display:grid;grid-template-columns:1.1fr .9fr;gap:0;background:#fff;border-radius:18px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 20px 80px rgba(2,6,23,.25)}
    @media (max-width:900px){.card{grid-template-columns:1fr}}
    .a{padding:28px 26px}
    .kicker{color:#64748b;font-size:13px}
    h1{margin:6px 0 12px;font-size:26px;color:#052e1a}
    p{color:#334155}
    .cta{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 16px;border-radius:12px;font-weight:700;border:1px solid transparent;cursor:pointer;text-decoration:none}
    .btn-primary{background:var(--emerald);color:#fff}
    .btn-primary:hover{background:#064e3b}
    .btn-gold{background:var(--gold);color:#0f172a}
    .btn-gold:hover{background:#d97706}
    .b{background:linear-gradient(180deg,#ecfdf5,transparent);padding:22px}
    .panel{height:100%;border:1px solid #e2e8f0;border-radius:14px;background:#fff;padding:18px;display:flex;flex-direction:column;justify-content:center}
    .list{display:grid;gap:10px;font-size:14px;color:#0f172a;margin-top:10px}
    .list div{display:flex;gap:10px;align-items:center}
    .dot{height:10px;width:10px;border-radius:999px;background:#059669}
  </style>
</head>
<body>
  <div class="card">
    <div class="a">
      <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" style="height:54px">
      <div class="kicker">Cavite State University Naic</div>
      <h1>Student ID Registration Portal</h1>
      <p>Register for your official university ID. Students sign in to access the registration form. Admins sign in to manage approvals.</p>
      <div class="cta">
        <a class="btn btn-primary" href="{{ route('login') }}">Log in</a>
        <a class="btn btn-gold" href="{{ route('create-account') }}">Create account</a>
      </div>
    </div>
    <div class="b">
      <div class="panel">
        <div class="kicker">What’s inside</div>
        <div class="list">
          <div><span class="dot"></span> Secure student login using student number</div>
          <div><span class="dot"></span> Guided, 3‑step ID registration form</div>
          <div><span class="dot"></span> Admin dashboard for approve/decline</div>
          <div><span class="dot"></span> Tailwind UI with responsive design</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
