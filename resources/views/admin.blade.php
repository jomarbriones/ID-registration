<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
    <title>Admin Dashboard - ID Registration Portal</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">
  <script>
    window.faceVerifierConfig = Object.assign({ modelPath: 'face-models' }, window.faceVerifierConfig || {});
  </script>
  <script src="{{ asset('js/face-verifier.js') }}"></script>

  <style>
    :root{
      --emerald:#16a34a;
      --emerald-700:#0f9f47;
      --rose:#E11D48;
      --ink:#0f172a;
      --muted:#67748a;
      --panel:#ffffff;
      --bg:#f6f5f0;
      --ring:#e5e7eb;
      --radius:14px;
      --shadow:0 18px 60px rgba(15,23,42,.12);
    }
    body.theme-dark{
      --ink:#e8edf6;
      --muted:#a6b3c7;
      --panel:#0f172a;
      --bg:#0b1220;
      --ring:#1f2937;
    }
    body.theme-dark{
      color:var(--ink);
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial;}
    body{background:var(--bg);color:var(--ink);min-height:100vh;}

    /* Shell */
    .wrap{max-width:1400px;margin:0 auto;padding:22px 28px 36px;}
    .topbar{position:sticky;top:0;z-index:8;display:flex;align-items:center;justify-content:space-between;padding:10px 0 14px;margin-bottom:6px;background:linear-gradient(180deg,var(--bg) 70%,rgba(246,245,240,0));backdrop-filter:blur(2px);}
    .brand{display:flex;align-items:center;gap:12px;}
    .logo{height:38px;width:38px;border-radius:12px;background:#0f172a;display:flex;align-items:center;justify-content:center;font-weight:800;box-shadow:0 10px 30px rgba(15,23,42,.16);}
    .kicker{font-size:12px;color:var(--muted);}

    .grid{display:grid;grid-template-columns:260px 1fr;gap:22px;align-items:start}
    @media (max-width:1080px){
      .grid{grid-template-columns:1fr;}
      .topbar{position:static;background:transparent;}
    }

    /* Sidebar */
    .side{
      position:sticky;
      top:20px;
      align-self:start;
      background:var(--panel);
      border:1px solid transparent;
      border-radius:22px;
      padding:16px 14px 18px;
      box-shadow:var(--shadow);
      display:flex;
      flex-direction:column;
      gap:8px;
    }
    .sidetop{display:flex;align-items:center;gap:12px;margin-bottom:10px}
    .sidetop img{box-shadow:0 12px 30px rgba(15,23,42,.12);}
    .sidetop .kicker{font-size:12px;color:var(--muted);}
    .nav-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin:16px 0 6px;padding:0 2px;}
    .nav{display:flex;flex-direction:column;gap:6px;margin-top:6px;flex:1}
    .navbtn{
      display:flex;align-items:center;gap:10px;
      padding:12px 12px;
      border-radius:14px;
      border:1px solid transparent;
      background:transparent;
      cursor:pointer;
      font-weight:700;
      color:var(--ink);
      position:relative;
      transition:background .18s,color .18s,box-shadow .18s;
    }
    .navbtn:hover{background:#f5f7fb;color:#0f1d3a;}
    .navbtn.active{background:#e2f5e9;color:#0f5132;box-shadow:inset 0 0 0 1px #c7ead8;}
    .navbtn .notify-dot{
      margin-left:auto;
      min-width:26px;
      padding:0 8px;
      height:22px;
      border-radius:999px;
      background:linear-gradient(135deg,#fb7185,#ef4444);
      color:#fff;
      font-size:11px;
      font-weight:800;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      box-shadow:0 0 0 2px #fff;
    }
    .nav-icon{height:18px;width:18px;display:inline-flex;align-items:center;justify-content:center;color:#0f1d3a;}
    .nav-footer{margin-top:auto;padding:10px 8px;display:flex;align-items:center;justify-content:space-between;border-radius:14px;background:transparent;}
    .nav-user{display:flex;align-items:center;gap:10px;min-width:0;}
    .nav-user-avatar{height:40px;width:40px;border-radius:12px;overflow:hidden;background:transparent;flex-shrink:0;}
    .nav-user-avatar img{height:100%;width:100%;object-fit:cover;}
    .nav-user-name{font-weight:800;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .nav-user-role{font-size:12px;color:var(--muted);}
    .nav-exit{height:40px;width:40px;border-radius:12px;background:transparent;color:#0f172a;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:.15s;box-shadow:none;border:1px solid transparent;}
    .nav-exit:hover{background:#eef2ff;}

    /* Card */
    .card{background:var(--panel);border:1px solid rgba(15,23,42,.08);border-radius:18px;overflow:hidden;box-shadow:var(--shadow);}
    .card-fixed{width:960px;min-width:960px;max-width:960px;flex:0 0 960px;position:relative;}
    .card-hd{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid rgba(15,23,42,.05)}
    .card-hd h2{font-size:18px}
    .card-sub{font-size:13px;color:var(--muted)}
    .card-bd{padding:18px}

    /* Dashboard */
    .hero-card{padding:0;overflow:hidden;}
    .hero-banner{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:22px 24px;border-bottom:1px solid rgba(15,23,42,.05);background:linear-gradient(135deg,#f7f7f2 0%,#f0f4ec 100%);}
    .hero-title{font-size:22px;font-weight:800;margin:2px 0;}
    .hero-meta{font-size:13px;color:var(--muted);}
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;padding:18px 20px;}
    .stat-card{padding:14px;border-radius:14px;background:#fff;border:1px solid rgba(15,23,42,.08);box-shadow:0 16px 30px rgba(15,23,42,.12);}
    .stat-label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;display:flex;align-items:center;gap:6px;}
    .stat-value{font-size:26px;font-weight:800;margin:6px 0;}
    .stat-sub{font-size:13px;color:var(--muted);}
    .placeholder-grid{padding:0 20px 20px;}
    .canvas-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:14px;}
    .placeholder-box{border:1px dashed var(--ring);border-radius:14px;padding:22px;background:#fbfbf7;color:var(--muted);font-weight:600;text-align:center;}
    .chart-card{background:#fff;border:1px solid rgba(15,23,42,.06);border-radius:14px;padding:16px;box-shadow:0 12px 30px rgba(15,23,42,.08);display:flex;flex-direction:column;gap:10px}
    .chart-title{font-weight:800;font-size:15px;color:#0f172a}
    .chart-sub{font-size:13px;color:var(--muted)}
    .bar-chart{display:flex;align-items:flex-end;gap:10px;height:140px;margin-bottom:10px;}
    .bar{flex:1;min-height:12px;border-radius:12px;position:relative;background:linear-gradient(180deg,#0ea5e9,#0f172a);box-shadow:0 10px 18px rgba(15,23,42,.12);}
    .bar.pending{background:linear-gradient(180deg,#fb923c,#f97316);}
    .bar.approved{background:linear-gradient(180deg,#22c55e,#15803d);}
    .bar.faculty{background:linear-gradient(180deg,#6366f1,#312e81);}
    .bar span{position:absolute;bottom:8px;left:50%;transform:translateX(-50%);font-weight:800;font-size:13px;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,.25);}
    .bar-label{display:none;}
    .chart-legend{display:flex;gap:10px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
    .legend-item{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid #e5e7eb;border-radius:10px;background:#f9fafb}
    .legend-dot{height:10px;width:10px;border-radius:999px;display:inline-block}
    .legend-pending{background:#f97316;}
    .legend-approved{background:#16a34a;}
    .legend-faculty{background:#4f46e5;}
    .progress-track{height:14px;border-radius:999px;background:#e5e7eb;overflow:hidden;position:relative;}
    .progress-fill{height:100%;background:linear-gradient(90deg,#16a34a,#0ea5e9);border-radius:999px;transition:width .2s;min-width:4%}
    .progress-meta{font-size:13px;color:var(--muted);display:flex;justify-content:space-between;align-items:center;margin-top:6px;gap:6px;flex-wrap:wrap}
    /* Responsive */
    @media(max-width:960px){
      .wrap{padding:16px 18px 30px;}
      .hero-banner{flex-direction:column;gap:10px;}
      .stat-grid{grid-template-columns:repeat(auto-fit,minmax(150px,1fr));}
      .canvas-grid{grid-template-columns:1fr;gap:10px;}
      .chart-card{padding:12px;}
      .card-bd{padding:14px;}
      .side{position:static;top:auto;}
      .nav-footer{justify-content:flex-start;gap:8px;}
    }
    @media(max-width:640px){
      .topbar{flex-direction:column;align-items:flex-start;gap:10px;}
      .stats-grid{grid-template-columns:1fr;}
      .hero-title{font-size:20px;}
      .search{width:100%;}
      .chart-legend{justify-content:flex-start;}
      .wizard-head{flex-direction:column;align-items:flex-start;}
      .wizard-progress{width:100%;}
      .grid{gap:14px;}
      .card-hd{padding:12px;}
      .card-bd{padding:12px;}
      .stat-card{padding:12px;}
      .side{width:88vw;border-radius:20px;}
      .nav{gap:10px;}
      .navbtn{padding:12px 14px;}
      .nav-footer{padding:14px 10px;}
      .chart-card{padding:12px;}
      .hero-banner{padding:16px;}
      .hero-meta{font-size:12px;}
      .bar-chart{height:120px;}
      .toast-wrap{right:10px;top:10px;}
    }
    /* Dark tweaks */
    body.theme-dark .card,
    body.theme-dark .settings-panel,
    body.theme-dark .settings-nav,
    body.theme-dark .stat-card,
    body.theme-dark .chart-card,
    body.theme-dark .wizard-shell{background:#0f172a;border-color:#1f2a3c;box-shadow:0 18px 48px rgba(0,0,0,.45);}
    body.theme-dark .stat-label,
    body.theme-dark .chart-sub,
    body.theme-dark .section-sub,
    body.theme-dark .meta{color:#cbd5e1;}
    body.theme-dark .bar-chart{background:transparent;}
    body.theme-dark .appearance-card{background:#182235;border-color:#1f2a3c;}
    body.theme-dark .navbtn{color:#e2e8f0;}
    body.theme-dark .navbtn.active{background:#0ea5e933;color:#e2e8f0;box-shadow:inset 0 0 0 1px #0ea5e9;}
    body.theme-dark .navbtn:hover{background:#111b2e;}
    body.theme-dark .nav-user-role{color:#cbd5e1;}
    body.theme-dark .nav-icon{color:#8fb5ff;}
    body.theme-dark .side{background:#0d1423;border-color:#1f2a3c;}
    body.theme-dark .navbtn .notify-dot{box-shadow:0 0 0 2px #0d1423;}
    body.theme-dark .hero-banner{background:linear-gradient(135deg,#0f172a 0%,#0b1220 100%);color:#e8edf6;border-color:#1f2a3c;}
    body.theme-dark .hero-title{color:#e8edf6;}
    body.theme-dark .hero-meta{color:#cbd5e1;}
    body.theme-dark .stat-value{color:#f8fafc;}
    body.theme-dark .stat-sub{color:#cbd5e1;}
    body.theme-dark .search,
    body.theme-dark .text-input,
    body.theme-dark .text-select{background:#0f192c;border-color:#1f2a3c;color:#e8edf6;box-shadow:none;}
    body.theme-dark .search::placeholder,
    body.theme-dark .text-input::placeholder{color:#94a3b8;}
    body.theme-dark .chip{box-shadow:none;}
    body.theme-dark .chip-soft{background:#1f2d47;color:#e8edf6;border-color:#30415f;}
    body.theme-dark .chip-outline{background:#1f2d47;color:#fca5a5;border-color:#f87171;}
    body.theme-dark .progress-track{background:#1f2a3c;}
    body.theme-dark .legend-item{background:#1f2a3c;border-color:#2c3b55;color:#cbd5e1;}
    body.theme-dark thead th{color:#e8edf6;}
    body.theme-dark .hero-card .stat-card{background:#0f172a;}
    body.theme-dark .placeholder-box{background:#111b2e;color:#cbd5e1;border-color:#1f2a3c;}
    body.theme-dark .hero-card{background:#0b1220;border-color:#1f2a3c;}
    body.theme-dark .stat-card{background:#0f192c;}
    body.theme-dark .helper-pill,
    body.theme-dark .count-pill{background:#182235;border-color:#2c3b55;color:#cbd5e1;}
    body.theme-dark .pill-note{background:#182235;border-color:#2c3b55;color:#cbd5e1;}
    body.theme-dark .wizard-head{background:#0f192c;border-color:#1f2a3c;}
    body.theme-dark .wizard-shell{background:#0c1526;}
    body.theme-dark .progress-track{background:#1f2a3c;}
    body.theme-dark .settings-nav button{background:#0f192c;border-color:#1f2a3c;color:#e8edf6;}
    body.theme-dark .settings-nav button .dot{background:#2c3b55;}
    body.theme-dark .settings-nav button.active{background:#0ea5e933;border-color:#0ea5e9;color:#e8edf6;}
    body.theme-dark .settings-nav button.active .dot{background:#0ea5e9;}
    body.theme-dark .appearance-card{background:#182235;border-color:#2c3b55;}
    body.theme-dark .modal{background:#0f172a;border-color:#1f2a3c;}
    body.theme-dark .profile-menu{background:#0f172a;border-color:#1f2a3c;}
    body.theme-dark .chip-emerald{background:#1fb36a;}
    body.theme-dark .helper-pill,
    body.theme-dark .count-pill{color:#dbe3f1;}
    body.theme-dark .chart-card,
    body.theme-dark .wizard-shell,
    body.theme-dark .card{border-color:#1f2a3c;}

    /* Search */
    .search{width:320px;max-width:100%;padding:10px 12px;border-radius:12px;border:1px solid var(--ring);outline:none;background:#fff;box-shadow:0 6px 16px rgba(15,23,42,.05);}
    .meta{font-size:12px;color:var(--muted);}

    /* Table */
    table{width:100%;border-collapse:collapse}
    thead th{font-size:12px;text-align:left;color:#0f5132;background:#edfbf2;border-bottom:1px solid rgba(15,23,42,.05);padding:10px}
    tbody td{padding:10px;border-bottom:1px solid rgba(15,23,42,.08);color:var(--ink);}
    tbody tr:hover td{background:#f8fafc}
    body.theme-dark table thead th{background:#0b1f36;color:#cbd5e1;border-color:#1f2a3c;}
    body.theme-dark table tbody td{border-color:#1f2a3c;color:#e8edf6;}
    body.theme-dark tbody tr:hover td{background:#14233a;}

    /* Buttons */
    .chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;line-height:1;border-radius:999px;padding:10px 14px;border:1px solid transparent;cursor:pointer;transition:.15s;box-shadow:0 10px 26px rgba(15,23,42,.08)}
    .chip-emerald{background:var(--emerald);color:#fff}.chip-emerald:hover{background:var(--emerald-700)}
    .chip-outline{background:#fff;border-color:#fecdd3;color:#be123c}.chip-outline:hover{background:#fff0f3}
    .chip-soft{background:#eef2ff;color:#3730a3}.chip-soft:hover{background:#e0e7ff}
    .chip:hover{transform:translateY(-1px);box-shadow:0 12px 30px rgba(15,23,42,.12);}
    body.theme-dark .chip-outline{background:#1f2d47;color:#fca5a5;border-color:#f87171}
    body.theme-dark .chip-outline:hover{background:#2a3a58;}
    body.theme-dark .chip-soft{background:#1f2d47;color:#e8edf6;border-color:#30415f;}
    body.theme-dark .chip-soft:hover{background:#243455;}
    body.theme-dark .chip-emerald:hover{background:#16c15a;}

    .helper-pill{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:999px;background:#f8fafc;border:1px solid var(--ring);color:var(--muted);font-size:12px;line-height:1.2}
    .count-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;font-size:12px;font-weight:700;line-height:1}
    /* Settings */
    .settings-grid{display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:start}
    @media(max-width:960px){.settings-grid{grid-template-columns:1fr}}
    .settings-nav{background:var(--panel);border:1px solid var(--ring);border-radius:14px;padding:12px;display:flex;flex-direction:column;gap:8px;box-shadow:0 10px 26px rgba(15,23,42,.06)}
    .settings-nav button{display:flex;align-items:center;gap:8px;width:100%;padding:12px;border-radius:12px;border:1px solid var(--ring);background:#fff;font-weight:700;cursor:pointer;text-align:left;transition:.12s;color:#0f172a}
    .settings-nav button .dot{height:12px;width:12px;border-radius:999px;background:#cbd5e1;display:inline-block}
    .settings-nav button.active{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}
    .settings-nav button.active .dot{background:#16a34a}
    .settings-panel{background:var(--panel);border:1px solid var(--ring);border-radius:14px;padding:16px;box-shadow:0 10px 22px rgba(15,23,42,.06)}
    .settings-panel h3{margin:0 0 6px;font-size:18px}
    .settings-panel .section-sub{color:var(--muted);font-size:13px;margin-bottom:14px}
    .settings-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px}
    .manage-btn{margin-bottom:12px;display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid #a7f3d0;background:#ecfdf5;color:#065f46;font-weight:800;cursor:pointer;box-shadow:0 10px 20px rgba(6,95,70,.12);}
    .toggle{width:56px;height:28px;border-radius:999px;position:relative;cursor:pointer;border:1px solid #cbd5e1;background:#e5e7eb;transition:.2s;display:inline-flex;align-items:center;padding:3px;}
    .toggle::after{content:'';width:22px;height:22px;border-radius:50%;background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.15);transition:.2s;}
    .toggle.on{background:linear-gradient(90deg,#16a34a,#0ea5e9);border-color:transparent;}
    .toggle.on::after{transform:translateX(28px);}
    .appearance-card{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px;border:1px solid var(--ring);border-radius:14px;background:#f8fafc;}
    .hamburger{display:none;align-items:center;justify-content:center;width:42px;height:42px;border-radius:12px;border:1px solid var(--ring);background:#fff;cursor:pointer;box-shadow:0 8px 20px rgba(15,23,42,.12);}
    .hamburger span{display:block;width:18px;height:2px;background:#0f172a;border-radius:4px;position:relative;}
    .hamburger span::before,.hamburger span::after{content:'';position:absolute;left:0;width:18px;height:2px;background:#0f172a;border-radius:4px;}
    .hamburger span::before{top:-6px;}
    .hamburger span::after{top:6px;}
    .nav-overlay{position:fixed;inset:0;background:rgba(2,6,23,.45);backdrop-filter:blur(2px);z-index:70;display:none;}
    @media(max-width:960px){
      .hamburger{display:flex;}
      .side{position:fixed;top:70px;left:0;height:calc(100vh - 70px);z-index:80;transform:translateX(-110%);transition:transform .2s ease;}
      .side.open{transform:translateX(0);}
      .nav-overlay{display:block;}
    }
    .top-actions{display:flex;align-items:center;gap:12px}
    .avatar-btn{height:40px;width:40px;border-radius:999px;overflow:hidden;border:2px solid var(--ring);cursor:pointer;display:flex;align-items:center;justify-content:center;background:#ecfdf5}
    .avatar-btn img{height:100%;width:100%;object-fit:cover}
    .profile-menu{position:absolute;right:0;top:48px;min-width:240px;background:#fff;border:1px solid var(--ring);border-radius:12px;box-shadow:0 12px 28px rgba(15,23,42,.12);display:none;z-index:70}
    .profile-menu.show{display:block}
    .profile-menu ul{list-style:none;margin:0;padding:6px}
    .profile-menu li button{width:100%;padding:10px 12px;border:none;background:transparent;text-align:left;border-radius:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px}
    .profile-menu li button:hover{background:#ecfdf5;color:#065f46}

    /* Modal */
    .backdrop{position:fixed;inset:0;background:rgba(2,6,23,.55);display:none;align-items:center;justify-content:center;z-index:50}
    .backdrop.show{display:flex}
    .modal{width:min(1100px,96vw);max-height:90vh;overflow:auto;background:#fff;border-radius:16px;border:1px solid var(--ring);box-shadow:0 30px 80px rgba(2,6,23,.25);transform:translateY(12px);opacity:0;transition:.18s}
    .backdrop.show .modal{transform:translateY(0);opacity:1}
    .modal-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--ring)}
    .modal-bd{padding:14px}
    .modal-content{display:flex;flex-direction:column;gap:18px}
    .modal-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px}
    .info-panel,.photo-panel{border:1px solid var(--ring);border-radius:14px;padding:18px;background:#f8fafc}
    .info-panel h3{margin:0;font-size:20px;font-weight:700;color:var(--ink)}
    .info-panel .sub{margin-top:4px;font-size:13px;color:var(--muted)}
    .info-list{margin-top:16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px}
    .info-list dt{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted)}
    .info-list dd{margin:2px 0 0;font-size:14px;font-weight:600;color:var(--ink)}
    .photo-panel{background:#fff;display:flex;flex-direction:column;align-items:center;text-align:center}
    .photo-panel img{width:100%;max-width:360px;border-radius:14px;border:1px solid var(--ring);object-fit:cover;aspect-ratio:3/4;background:#f8fafc}
    .photo-panel .status{margin-top:10px;font-size:13px;color:var(--muted)}
    .modal-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:12px}
    .modal-actions button{min-width:150px;border-radius:999px;border:1px solid transparent;padding:12px 18px;font-weight:700;cursor:pointer;transition:.15s}
    .modal-actions .approve{background:var(--emerald);color:#fff}
    .modal-actions .approve:hover{background:var(--emerald-700)}
    .modal-actions .decline{background:#fff5f5;color:#b91c1c;border-color:#fecdd3}
    .modal-actions .decline:hover{background:#ffe4e6}
    .modal-state{padding:24px;text-align:center;font-size:14px;color:var(--muted)}
    .modal-preview-shell{border:1px dashed var(--ring);border-radius:14px;background:#fff}
    .modal-preview-shell h4{margin:0;padding:14px 18px;border-bottom:1px solid var(--ring);font-size:14px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em}
    .modal-preview-shell .preview-sheet{border-top:1px solid transparent}
    .xbtn{background:#f1f5f9;border:1px solid var(--ring);border-radius:10px;padding:6px 10px;cursor:pointer}

    /* Toasts */
    .toast-wrap{position:fixed;right:16px;top:16px;display:flex;flex-direction:column;gap:10px;z-index:60}
    .toast{min-width:240px;max-width:360px;padding:10px 12px;border-radius:12px;color:#fff;display:flex;justify-content:space-between;gap:10px;opacity:0;transform:translateY(-6px);transition:.18s}
    .toast.show{opacity:1;transform:translateY(0)}
    .toast.ok{background:#059669}.toast.err{background:#b91c1c}
    .toast .close{background:transparent;border:none;color:#fff;font-weight:900;cursor:pointer}

    /* Small helper */
    .grid-col{display:grid;grid-template-columns:1fr;gap:14px}
    .split{display:grid;grid-template-columns:1fr;gap:16px}
    .wizard-layout{grid-template-columns:minmax(360px,1fr) 1fr;align-items:start}
    .card-wide{min-width:640px;}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(240px,1fr));gap:14px}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-label{font-size:13px;font-weight:700;color:var(--ink)}
    .text-input, .text-select{width:100%;padding:12px 14px;border-radius:12px;border:1px solid #e5e7eb;background:#f9fafb;font-size:14px;color:var(--ink);outline:none;transition:border-color .15s, box-shadow .15s, background .15s}
    input.text-input{text-transform:capitalize;}
    .text-input:focus, .text-select:focus{border-color:#a7f3d0;box-shadow:0 10px 30px rgba(15,23,42,.06),0 0 0 3px #ecfdf3;background:#fff}
    .helper-text{font-size:12px;color:var(--muted)}
    .wizard-head{display:flex;align-items:center;gap:12px;margin-bottom:10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:14px;background:#f8fafc}
    .wizard-pill{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:999px;background:#fff;border:1px solid #e5e7eb;font-weight:700;font-size:12px;color:#0f172a}
    .wizard-progress{flex:1;height:10px;background:#e5e7eb;border-radius:999px;overflow:hidden;min-width:180px;box-shadow:inset 0 1px 0 rgba(255,255,255,.7)}
    .wizard-progress span{display:block;height:100%;background:linear-gradient(90deg,#16a34a,#0ea5e9);transition:width .2s}
    .wizard-shell{display:flex;flex-direction:column;gap:14px;padding:14px;border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 14px 36px rgba(15,23,42,.08);}
    .wizard-actions{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-top:4px;flex-wrap:wrap}
    .wizard-buttons{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .pill-note{padding:12px 14px;border-radius:14px;border:1px dashed #d4d8e0;background:#f8fafc;font-size:12px;color:var(--muted);line-height:1.4}
  </style>
</head>
<body>

    <div class="wrap" x-data="adminApp()" x-init="boot()">
    <!-- top -->
    <div class="topbar">
      <button class="hamburger" @click="navOpen = !navOpen" aria-label="Toggle navigation">
        <span></span>
      </button>
      <div class="brand">
        <div class="logo" style="background:#065f46">
          <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" alt="University logo" style="height:100%;width:100%;object-fit:cover;border-radius:999px">
        </div>
        <div>
          <div style="font-weight:800">Admin Dashboard</div>
          <div class="kicker">Cavite State University - ID Portal</div>
        </div>
      </div>
      <div class="top-actions" style="position:relative">
        <div class="helper-pill">
          <div style="font-weight:700">Signed in</div>
          <div class="kicker">&bull; Secure session</div>
        </div>
        <button class="avatar-btn" @click="profileMenuOpen = !profileMenuOpen">
          <img :src="currentUser?.photo_url || defaultAvatar" alt="Profile">
        </button>
        <div class="profile-menu" x-show="profileMenuOpen" x-cloak :class="{show: profileMenuOpen}" @click.outside="profileMenuOpen=false">
          <ul>
            <li><button type="button" @click="openSettingsTab('account')">My Account</button></li>
            <li><button type="button" @click="openSettingsTab('enrolled')">Manage Enrolled Students</button></li>
            <li><button type="button" @click="openSettingsTab('appearance')">Appearance</button></li>
            <li><button type="button" @click="logout()">Log out</button></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Faculty Preview Modal -->
    <div class="backdrop" :class="{show:facultyModalOpen}" x-show="facultyModalOpen" @click.self="facultyModalOpen=false" @keydown.escape.window="facultyModalOpen=false">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="faculty-preview-title">
        <div class="modal-hd">
          <div>
            <div id="faculty-preview-title" style="font-weight:800">Faculty Preview</div>
          </div>
          <button class="xbtn" @click="facultyModalOpen=false">Close</button>
        </div>
        <div class="modal-bd">
          <div x-show="facultyModalLoading" class="modal-state" x-cloak>Loading...</div>
          <div x-show="!facultyModalLoading && facultyModalError" class="modal-state" style="color:#b91c1c" x-text="facultyModalError" x-cloak></div>
          <div class="modal-preview-shell" x-show="!facultyModalLoading && !facultyModalError" x-cloak>
            <h4>ID Card Layout</h4>
            <div class="preview-sheet" style="display:flex;justify-content:center;padding:12px;overflow:visible">
              <div style="min-width:14cm;max-width:100%;display:flex;justify-content:center" x-html="facultyModalCardHtml || `<div style='padding:18px;color:#64748b'>No preview available.</div>`"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="nav-overlay" x-show="navOpen && isMobile" x-transition.opacity @click="navOpen=false" x-cloak></div>

    <div class="grid">
      <!-- sidebar -->
      <aside class="side" :class="{'open': navOpen}">
        <div class="sidetop">
          <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" alt="" style="height:34px;width:34px;border-radius:8px;background:#fff;padding:6px;border:1px solid var(--ring)">
          <div>
            <div style="font-weight:700">Admin</div>
            <div class="kicker">ID Registration Portal</div>
          </div>
        </div>

        <nav class="nav">
          <div class="nav-label">Main</div>
          <button class="navbtn active" data-nav="dashboard" @click="show('dashboard',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m4 10.5 8-6.5 8 6.5v7.5a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-4h-4v4a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Z"/></svg>
            </span>
            <span>Dashboard</span>
          </button>
          <button class="navbtn" data-nav="pending" @click="show('pending',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 6.5h14M5 12h7m-7 5.5h5" /><rect x="3.5" y="4" width="17" height="16" rx="3"/></svg>
            </span>
            <span>Pending Approvals</span>
            <span class="notify-dot" x-show="counts.pending>0" x-text="counts.pending" style="display:none;"></span>
          </button>
          <button class="navbtn" data-nav="approved" @click="show('approved',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="m9 10 2 2 4-4"/></svg>
            </span>
            <span>Registered Students</span>
          </button>

          <div class="nav-label">Faculty</div>
          <button class="navbtn" data-nav="faculty" @click="show('faculty',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3.5" width="16" height="17" rx="3"/><path d="M9 9h6m-6 3.5h4.5m-2.5 6v-3"/></svg>
            </span>
            <span>Create Faculty ID</span>
          </button>
          <button class="navbtn" data-nav="facultyList" @click="show('facultyList',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15.5 6a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"/><path d="M4 19.5c.18-3.2 2.91-5.5 5.5-5.5s5.32 2.28 5.5 5.5M16.5 11.5c1.38 0 2.5 1.12 2.5 2.5M15 19.5c.12-1.74 1.26-3 2.85-3 .9 0 1.72.35 2.29.93.55.57.86 1.36.86 2.07"/></svg>
            </span>
            <span>Faculty List</span>
          </button>

          <div class="nav-label">System</div>
          <button class="navbtn" data-nav="settings" @click="settingsTab='account';show('settings',$event)">
            <span class="nav-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="m4.93 6.5 1.14 1.98M3.5 12h2.28m-.95 5.5 1.14-1.98M12 20.72V18.5m5.5.95-1.98-1.14M20.5 12h-2.22m.92-5.5-1.98 1.14M12 3.28V5.5"/></svg>
            </span>
            <span>Settings</span>
          </button>
        </nav>

        <div class="nav-footer">
          <div class="nav-user">
           <div class="nav-user-avatar">
              <img :src="accountPhotoUrl || currentUser?.photo_url || defaultAvatar" alt="Profile avatar">
            </div>
            <div>
              <div class="nav-user-name" x-text="currentUser?.name || 'Admin User'"></div>
              <div class="nav-user-role">Administrator</div>
            </div>
          </div>
          <button class="nav-exit" type="button" @click="logout()" aria-label="Logout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" height="18" width="18">
              <path d="M15 17.5V19a2 2 0 0 1-2 2H6.5A2.5 2.5 0 0 1 4 18.5V5.5A2.5 2.5 0 0 1 6.5 3H13a2 2 0 0 1 2 2v1.5"/>
              <path d="m10.5 12 8-4.5v9Z"/>
            </svg>
          </button>
        </div>
      </aside>

      <!-- content -->
      <main class="grid-col">

        <!-- Dashboard -->
        <section class="card hero-card" x-show="tab==='dashboard'" x-transition.opacity>
          <div class="hero-banner">
            <div>
              <div class="kicker">Overview</div>
              <div class="hero-title">Registration workspace</div>
              <div class="hero-meta">Quick glance at program health — tap any stat to jump into details.</div>
            </div>
          </div>
          <div class="stat-grid">
            <div class="stat-card">
              <div class="stat-label">Pending approvals</div>
              <div class="stat-value" x-text="counts.pending || 0"></div>
              <div class="stat-sub">Awaiting review</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Registered students</div>
              <div class="stat-value" x-text="counts.approved || 0"></div>
              <div class="stat-sub">Ready for printing</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Enrolled students</div>
              <div class="stat-value" x-text="counts.enrolled || 0"></div>
              <div class="stat-sub">From enrolled_students</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Faculty IDs</div>
              <div class="stat-value" x-text="counts.faculty || 0"></div>
              <div class="stat-sub">Created records</div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Last update</div>
              <div class="stat-value" x-text="updatedAt || '—'"></div>
              <div class="stat-sub">Refresh to sync</div>
            </div>
          </div>
          <div class="placeholder-grid">
            <div class="canvas-grid">
              <div class="chart-card">
                <div class="chart-title">Pipeline overview</div>
                <div class="chart-sub">Relative volume of pending, approved, and faculty IDs.</div>
                <div class="bar-chart">
                  <div class="bar pending" :style="`height:${Math.min(140,Math.max(14,(counts.pending||0)*6))}px`">
                    <span x-text="counts.pending || 0"></span>
                  </div>
                  <div class="bar approved" :style="`height:${Math.min(140,Math.max(14,(counts.approved||0)*6))}px`">
                    <span x-text="counts.approved || 0"></span>
                  </div>
                  <div class="bar faculty" :style="`height:${Math.min(140,Math.max(14,(counts.faculty||0)*6))}px`">
                    <span x-text="counts.faculty || 0"></span>
                  </div>
                </div>
                <div class="chart-legend">
                  <div class="legend-item"><span class="legend-dot legend-pending"></span>Pending</div>
                  <div class="legend-item"><span class="legend-dot legend-approved"></span>Approved</div>
                  <div class="legend-item"><span class="legend-dot legend-faculty"></span>Faculty</div>
                </div>
              </div>
              <div class="chart-card">
                <div class="chart-title">Enrollment coverage</div>
                <div class="chart-sub">Approved vs enrolled baseline.</div>
                <div class="progress-track">
                  <div class="progress-fill" :style="`width:${coveragePercent()}%;`"></div>
                </div>
                <div class="progress-meta">
                  <div><strong x-text="counts.approved || 0"></strong> approved of <strong x-text="counts.enrolled || 0"></strong> enrolled</div>
                  <div><strong x-text="coveragePercent() + '%'"></strong> coverage</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Pending -->
        <section class="card" x-show="tab==='pending'" x-transition.opacity>
          <div class="card-hd">
            <div>
              <h2>Pending Approvals</h2>
              <div class="card-sub">Manage submissions and approvals</div>
            </div>
            <div class="meta" x-text="updatedAt"></div>
          </div>
          <div class="card-bd">
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;gap:12px;align-items:center;flex-wrap:wrap;">
              <input id="pending-search" name="pending_search" class="search" style="flex:1 1 260px" placeholder="Search pending..." @input="filterTable('pending', $event.target.value)">
            </div>

            <div style="overflow:auto">
              <table id="pendingTable">
                <thead>
                  <tr>
                    <th>Student #</th><th>First</th><th>Last</th><th>Course</th><th>Date</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="pending-body">
                  <tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Approved -->
        <section class="card" x-show="tab==='approved'" x-transition.opacity>
          <div class="card-hd">
            <div>
              <h2>Registered Students</h2>
              <div class="card-sub">Approved records</div>
            </div>
            <div class="meta" x-text="updatedAt"></div>
          </div>
          <div class="card-bd">
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;gap:12px;align-items:center;flex-wrap:wrap;">
              <input id="approved-search" name="approved_search" class="search" style="flex:1 1 260px" placeholder="Search students..." @input="filterTable('approved', $event.target.value)">
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="count-pill" x-show="sel.approved.length>0" x-text="sel.approved.length + (sel.approved.length===1 ? ' student selected' : ' students selected')"></span>
                <button class="chip chip-emerald" :disabled="sel.approved.length===0" @click="printSelected('approved')">Print IDs</button>
              </div>
            </div>

            <div style="overflow:auto">
              <table id="approvedTable">
                <thead>
                  <tr>
                    <th><input id="approved-toggle-all" name="approved_toggle_all" type="checkbox" @change="toggleAll('approved',$event)"></th>
                    <th>Student #</th><th>First</th><th>Last</th><th>Course</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="approved-body">
                  <tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Faculty -->
        <section class="card" x-show="tab==='faculty'" x-transition.opacity>
          <div class="card-hd">
            <div>
              <h2>Create Faculty ID</h2>
              <div class="card-sub">Save faculty details, preview the ID, and print a centered A4 copy.</div>
            </div>
            <div class="meta" x-text="facultyPreview?.id ? `Record #${facultyPreview.id}` : ''"></div>
          </div>
          <div class="card-bd">
            <div class="split wizard-layout" :style="facultyStep===2 ? 'grid-template-columns:minmax(360px,1fr) 1fr;' : 'grid-template-columns:1fr;'">
              <form class="grid-col wizard-shell" @submit.prevent="submitFaculty()" enctype="multipart/form-data" novalidate>
                <div class="wizard-head">
                  <div class="wizard-pill">Step <span x-text="facultyStep+1"></span> of 3</div>
                  <div class="wizard-progress">
                    <span :style="`width:${(facultyStep+1)/3*100}%;`"></span>
                  </div>
                </div>

                <section x-show="facultyStep===0" x-cloak class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="faculty-first">First Name</label>
                    <input id="faculty-first" name="first_name" type="text" class="text-input" placeholder="First name" x-model="facultyForm.first_name" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-middle">M.I.</label>
                    <input id="faculty-middle" name="middle_initial" type="text" class="text-input" placeholder="M" maxlength="2" x-model="facultyForm.middle_initial">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-last">Last Name</label>
                    <input id="faculty-last" name="last_name" type="text" class="text-input" placeholder="Last name" x-model="facultyForm.last_name" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-position">Position</label>
                    <input id="faculty-position" name="position" type="text" class="text-input" placeholder="e.g., Assistant Professor I" x-model="facultyForm.position">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-gender">Gender</label>
                    <select id="faculty-gender" name="gender" class="text-select" x-model="facultyForm.gender">
                      <option value="">Select</option>
                      <option value="MALE">Male</option>
                      <option value="FEMALE">Female</option>
                      <option value="OTHER">Other</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-blood">Blood Type</label>
                    <select id="faculty-blood" name="blood_type" class="text-select" x-model="facultyForm.blood_type">
                      <option value="">Select</option>
                      <option value="A+">A+</option><option value="A-">A-</option>
                      <option value="B+">B+</option><option value="B-">B-</option>
                      <option value="AB+">AB+</option><option value="AB-">AB-</option>
                      <option value="O+">O+</option><option value="O-">O-</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-civil">Civil Status</label>
                    <select id="faculty-civil" name="civil_status" class="text-select" x-model="facultyForm.civil_status">
                      <option value="">Select</option>
                      <option value="Single">Single</option>
                      <option value="Married">Married</option>
                      <option value="Widowed">Widowed</option>
                      <option value="Separated">Separated</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-bday">Birthday</label>
                    <input id="faculty-bday" name="birthday" type="date" class="text-input" x-model="facultyForm.birthday">
                  </div>
                </section>

                <section x-show="facultyStep===1" x-cloak class="form-grid">
                  <div class="form-group">
                    <label class="form-label">ID Photo</label>
                    <input id="faculty-photo" name="photo" type="file" accept="image/jpeg,image/png" x-ref="facultyPhoto" class="text-input" @change="e=>facultyPhotoUrl = e.target.files[0] ? URL.createObjectURL(e.target.files[0]) : ''">
                    <div class="helper-text">Square 1x1 style photo. Max 5MB.</div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-emer-name">Emergency Contact Name</label>
                    <input id="faculty-emer-name" name="emergency_contact_name" type="text" class="text-input" x-model="facultyForm.emergency_contact_name">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-emer-number">Emergency Contact Number</label>
                    <input id="faculty-emer-number" name="emergency_contact_number" type="text" inputmode="numeric" pattern="\\d*" class="text-input" x-model="facultyForm.emergency_contact_number" @input="$event.target.value=$event.target.value.replace(/[^0-9]/g,'')">
                  </div>
                  <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label" for="faculty-emer-address">Emergency Contact Address</label>
                    <input id="faculty-emer-address" name="emergency_contact_address" type="text" class="text-input" x-model="facultyForm.emergency_contact_address">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-gsis">GSIS Number</label>
                    <input id="faculty-gsis" name="gsis_number" type="text" inputmode="numeric" pattern="\\d*" class="text-input" x-model="facultyForm.gsis_number" placeholder="Digits only" @input="$event.target.value=$event.target.value.replace(/[^0-9]/g,'')">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-sss">SSS Number</label>
                    <input id="faculty-sss" name="sss_number" type="text" inputmode="numeric" pattern="\\d*" class="text-input" x-model="facultyForm.sss_number" placeholder="Digits only" @input="$event.target.value=$event.target.value.replace(/[^0-9]/g,'')">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="faculty-tin">TIN Number</label>
                    <input id="faculty-tin" name="tin_number" type="text" inputmode="numeric" pattern="\\d*" class="text-input" x-model="facultyForm.tin_number" placeholder="Digits only" @input="$event.target.value=$event.target.value.replace(/[^0-9]/g,'')">
                  </div>
                </section>

                <section x-show="facultyStep===2" x-cloak class="form-grid">
                  <div style="grid-column:1/-1">
                    <label class="form-label" for="faculty-confirm">
                      <input id="faculty-confirm" name="confirm_truth" type="checkbox" x-model="facultyConfirm" style="margin-right:6px">
                      I confirm all information is true and correct.
                    </label>
                  </div>
                </section>

                <div class="wizard-actions">
                  <div class="pill-note">Complete the steps then Save (preview is available on Step 3). The preview on the right reads from <strong>id_registration_db</strong>.</div>
                  <div class="wizard-buttons">
                    <button class="chip chip-soft" type="button" x-show="facultyStep>0" @click="goBack()">Back</button>
                    <button class="chip chip-soft" type="button" x-show="facultyStep<2" @click="goNext()" :disabled="!canProceed(facultyStep)">Next</button>
                    <div class="wizard-buttons" x-show="facultyStep===2" x-cloak>
                      <button type="submit" class="chip chip-emerald" style="padding:10px 16px;border-radius:12px" :disabled="facultySaving">
                        <span x-show="!facultySaving">Save</span>
                        <span x-show="facultySaving">Saving...</span>
                      </button>
                      <button type="button" class="chip chip-outline" style="padding:10px 16px;border-radius:12px" @click="resetFacultyForm()">Clear</button>
                    </div>
                  </div>
                </div>

                <div class="meta" style="color:#b91c1c" x-show="facultyError" x-text="facultyError"></div>
                <div class="meta" style="color:#047857;font-weight:700" x-show="facultySuccess" x-text="facultySuccess"></div>
              </form>

              <div class="grid-col">
                <div class="modal-preview-shell" x-show="facultyStep===2" x-cloak>
                  <h4>Faculty ID Preview</h4>
                  <div class="preview-sheet" style="display:flex;justify-content:center;padding:12px;overflow:visible">
                    <div style="min-width:14cm;max-width:100%;display:flex;justify-content:center" x-html="facultyCardHtml || `<div style='padding:18px;color:#64748b'>Save a record to generate the ID preview.</div>`"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Faculty List -->
        <section class="card card-fixed" style="margin:0 auto" x-show="tab==='facultyList'" x-transition.opacity>
          <div class="card-hd">
            <div>
              <h2>Faculty ID List</h2>
              <div class="card-sub">Manage faculty records, preview, edit, and print IDs.</div>
            </div>
            <div class="meta" x-text="facultyUpdatedAt"></div>
          </div>
          <div class="card-bd">
            <div style="display:flex;justify-content:space-between;margin-bottom:10px;gap:12px;align-items:center;flex-wrap:wrap;">
              <input id="faculty-search" name="faculty_search" class="search" style="flex:1 1 260px" placeholder="Search faculty..." @input="filterTable('faculty', $event.target.value)">
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="count-pill" x-show="sel.faculty.length>0" x-text="sel.faculty.length + (sel.faculty.length===1 ? ' record selected' : ' records selected')"></span>
                <button class="chip chip-emerald" :disabled="sel.faculty.length===0" @click="printSelectedFaculty()">Print IDs</button>
              </div>
            </div>

            <div style="overflow:auto">
              <table id="facultyTable">
                <thead>
                  <tr>
                    <th><input id="faculty-toggle-all" name="faculty_toggle_all" type="checkbox" @change="toggleAll('faculty',$event)"></th>
                    <th>ID Number</th><th>Last Name</th><th>First Name</th><th>M.I.</th><th>Position</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="faculty-body">
                  <tr><td colspan="7" style="padding:16px;text-align:center;color:var(--muted)">Loading...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Settings -->
        <section class="card" x-show="tab==='settings'" x-transition.opacity>
          <div class="card-hd"><h2>Settings</h2></div>
          <div class="card-bd">
            <div class="settings-grid">
              <div class="settings-nav">
                <button :class="{active: settingsTab==='account'}" @click="settingsTab='account'"><span class="dot"></span> Personal Information</button>
                <button :class="{active: settingsTab==='enrolled'}" @click="settingsTab='enrolled'"><span class="dot"></span> Enrolled Students</button>
                <button :class="{active: settingsTab==='appearance'}" @click="settingsTab='appearance'"><span class="dot"></span> Appearance</button>
              </div>
              <div class="settings-panel">
                <template x-if="settingsTab==='account'">
                  <div>
                    <h3>Personal information</h3>
                    <div class="section-sub">Update your profile details. This is shared with other admins.</div>
                    <button type="button" class="manage-btn" @click="accountLocked=false">Manage account</button>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:12px">
                      <label class="chip chip-soft" style="cursor:pointer; border:1px dashed var(--ring);" :class="{'chip-soft': !accountLocked, 'chip': accountLocked}" :style="accountLocked ? 'opacity:.6;pointer-events:none;' : ''">
                        <input type="file" accept="image/*" x-ref="accountPhoto" @change="handleAccountPhoto($event)" style="display:none" :disabled="accountLocked">
                        <span>Upload profile picture</span>
                      </label>
                      <div class="meta" x-show="accountPhotoName" x-text="accountPhotoName"></div>
                    </div>
                    <div class="settings-form">
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">First name</label>
                        <input type="text" class="search" style="width:100%" x-model="accountFirst" placeholder="First name" :disabled="accountLocked">
                      </div>
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">Last name</label>
                        <input type="text" class="search" style="width:100%" x-model="accountLast" placeholder="Last name" :disabled="accountLocked">
                      </div>
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">Email / username</label>
                        <input type="text" class="search" style="width:100%" x-model="accountUsername" placeholder="admin email or username" :disabled="accountLocked">
                      </div>
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">Display name</label>
                        <input type="text" class="search" style="width:100%" x-model="accountName" placeholder="Name shown to users" :disabled="accountLocked">
                      </div>
                    </div>
                    <div class="settings-form" style="margin-top:8px">
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">Current password</label>
                        <input type="password" class="search" style="width:100%" x-model="accountPassCurrent" placeholder="Current password" :disabled="accountLocked">
                      </div>
                      <div>
                        <label style="display:block;font-weight:700;font-size:13px">New password</label>
                        <input type="password" class="search" style="width:100%" x-model="accountPassNew" placeholder="New password" :disabled="accountLocked">
                      </div>
                    </div>
                    <div class="meta" style="color:#b91c1c;margin-top:8px" x-show="accountError" x-text="accountError"></div>
                    <div class="meta" style="color:#047857;font-weight:600;margin-top:4px" x-show="accountStatus" x-text="accountStatus"></div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:12px">
                      <button type="button" class="chip chip-outline" @click="resetAccount" :disabled="accountBusy">Cancel</button>
                      <button type="button" class="chip chip-emerald" style="border:none;padding:10px 18px" :disabled="accountBusy || accountLocked"
                              @click="saveAccount">
                        <span x-show="!accountBusy">Save</span>
                        <span x-show="accountBusy">Saving...</span>
                      </button>
                    </div>
                  </div>
                </template>

                <template x-if="settingsTab==='enrolled'">
                  <div>
                    <h3>Manage enrolled students</h3>
                    <div class="section-sub">
                      Upload the official enrolled-student list (.xlsx). Expected headers: <strong>ID Number</strong>,
                      <strong>First_Name</strong>, <strong>Last_Name</strong>, <strong>Middle_Initial</strong>, <strong>Course</strong>.
                    </div>
                    <form @submit.prevent="submitRoster" x-ref="rosterForm" style="display:flex;flex-direction:column;gap:12px">
                      <div>
                        <label for="roster-file" style="display:block;font-weight:700;font-size:13px">Excel roster (.xlsx)</label>
                        <input id="roster-file" name="roster" type="file" accept=".xlsx"
                               x-ref="rosterFile"
                               @change="rosterFileName = $event.target.files?.[0]?.name || ''"
                               style="margin-top:6px;width:100%;padding:10px;border-radius:10px;border:1px dashed var(--ring);background:#fff">
                        <div class="meta" x-show="rosterFileName" x-text="rosterFileName" style="margin-top:4px"></div>
                      </div>
                      <label style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600">
                        <input type="checkbox" x-model="rosterReplace">
                        <span>Replace existing enrolled-student records instead of merging</span>
                      </label>
                      <div class="helper-pill">
                        <div>
                          Ensure the first row contains column headers. Any row missing an ID number, first name, or last name will be skipped.
                        </div>
                      </div>
                      <div class="meta" style="color:#b91c1c" x-show="rosterError" x-text="rosterError"></div>
                      <div class="meta" style="color:#047857;font-weight:600" x-show="rosterStatus" x-text="rosterStatus"></div>
                      <div style="display:flex;justify-content:flex-end;gap:10px">
                        <button type="submit" class="chip chip-emerald" :disabled="rosterBusy" style="border:none;padding:10px 18px">
                          <span x-show="!rosterBusy">Upload roster</span>
                          <span x-show="rosterBusy">Uploading...</span>
                        </button>
                      </div>
                    </form>
                  </div>
                </template>

                <template x-if="settingsTab==='appearance'">
                  <div>
                    <h3>Appearance</h3>
                    <div class="section-sub">Switch theme to reduce glare. Your preference is remembered on this device.</div>
                    <div class="appearance-card">
                      <div>
                        <div style="font-weight:700;margin-bottom:4px">Dark mode</div>
                        <div class="section-sub" style="margin:0;">Match university palette for low-light viewing.</div>
                      </div>
                      <button type="button" class="toggle" :class="{on:isDarkMode}" @click="isDarkMode=!isDarkMode; toggleTheme()" aria-label="Toggle dark mode"></button>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </section>

      </main>
    </div>

    <!-- MODAL -->
    <div class="backdrop" :class="{show:modalOpen}" @click.self="closeModal()" @keydown.escape.window="closeModal()">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="pv-title">
        <div class="modal-hd">
          <div>
            <div id="pv-title" style="font-weight:800" x-text="modalStudent ? modalStudent.full_name : 'Student Preview'">Student Preview</div>
            <div class="meta" x-show="modalStudent" x-text="modalStudent ? `Student #${modalStudent.id_number}` : ''"></div>
          </div>
          <button class="xbtn" @click="closeModal()">Close</button>
        </div>
        <div class="modal-bd">
          <div x-show="modalLoading" class="modal-state" x-cloak>Loading...</div>
          <div x-show="!modalLoading && modalError" class="modal-state" style="color:#b91c1c" x-text="modalError" x-cloak></div>
          <template x-if="!modalLoading && modalStudent">
            <div class="modal-content" x-cloak>
              <div class="modal-grid">
                <section class="info-panel">
                  <h3 x-text="modalStudent.full_name || 'â€”'"></h3>
                  <div class="sub" x-text="`Student #${modalStudent.id_number || 'â€”'}`"></div>
                  <dl class="info-list">
                    <div>
                      <dt>Course</dt>
                      <dd x-text="modalStudent.course || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Gender</dt>
                      <dd x-text="modalStudent.gender || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Blood Type</dt>
                      <dd x-text="modalStudent.blood_type || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Address</dt>
                      <dd x-text="modalStudent.address || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Guardian</dt>
                      <dd x-text="modalStudent.guardian_name || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Guardian Address</dt>
                      <dd x-text="modalStudent.parent_address || 'â€”'"></dd>
                    </div>
                    <div>
                      <dt>Guardian Contact</dt>
                      <dd x-text="modalStudent.guardian_contact || 'â€”'"></dd>
                    </div>
                  </dl>
                  <div class="meta" style="margin-top:14px" x-show="modalStudent.submitted_at_for_display" x-text="modalStudent.submitted_at_for_display ? `Submitted ${modalStudent.submitted_at_for_display}` : ''"></div>
                </section>
                <section class="photo-panel">
                  <img :src="modalStudent.photo_url" :alt="modalStudent.full_name ? `Uploaded photo of ${modalStudent.full_name}` : 'Uploaded photo'" loading="lazy" onerror="this.src='{{ asset('images/photo-placeholder.png') }}'">
                  <div class="status" x-show="modalStudent.status_label" x-text="`Status: ${modalStudent.status_label}`"></div>
                </section>
              </div>
              <div class="modal-actions" x-show="modalStudent?.status === 'pending'" x-cloak>
                <button type="button" class="decline" @click="confirmAction('decline', modalStudent.id, { name: modalStudent.full_name, number: modalStudent.id_number })">Reject</button>
                <button type="button" class="approve" @click="confirmAction('approve', modalStudent.id, { name: modalStudent.full_name, number: modalStudent.id_number })">Approve</button>
              </div>
              <div class="modal-preview-shell" x-show="modalCardHtml" x-cloak>
                <h4>ID Card Layout</h4>
                <div x-html="modalCardHtml"></div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- Refresh Photo -->
    <div class="backdrop" :class="{show:refreshOpen}" x-show="refreshOpen" @click.self="closeRefresh()" @keydown.escape.window="closeRefresh()">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="refresh-title">
        <div class="modal-hd">
          <div id="refresh-title" style="font-weight:800">Refresh Reference Photo</div>
          <button class="xbtn" @click="closeRefresh()">Close</button>
        </div>
        <div class="modal-bd">
          <form x-ref="refreshForm" @submit.prevent="submitRefresh" style="display:flex;flex-direction:column;gap:14px">
            <div>
              <div style="font-size:14px;font-weight:700" x-text="refreshStudent?.name ?? 'No student selected'"></div>
              <div class="meta" x-text="refreshStudent ? `Student #${refreshStudent.number}` : ''"></div>
            </div>
            <div>
              <label for="refresh-photo" style="display:block;font-size:13px;font-weight:600;color:#0f172a">New 1x1 Photo</label>
              <input id="refresh-photo" name="refresh_photo" type="file" x-ref="refreshFile" accept="image/jpeg,image/png" @change="handleRefreshFile($event)"
                     style="margin-top:6px;width:100%;padding:10px;border:1px dashed var(--ring);border-radius:10px;background:#fff">
              <input type="hidden" name="face_embedding" x-ref="refreshEmbedding">
              <div class="meta" style="margin-top:6px;color:#047857;font-weight:600" x-show="refreshStatus" x-text="refreshStatus"></div>
              <div class="meta" style="margin-top:6px;color:#b91c1c" x-show="refreshError" x-text="refreshError"></div>
            </div>
            <div class="meta" style="font-size:12px;line-height:1.4">
              Upload a square, well-lit photo. We will compare it against the existing reference before saving.
            </div>
            <div style="display:flex;justify-content:flex-end;gap:10px">
              <button type="button" class="xbtn" style="padding:10px 16px" @click="closeRefresh()">Cancel</button>
              <button type="submit" class="chip chip-emerald" :disabled="refreshBusy" style="border:none;padding:10px 18px">
                <span x-show="!refreshBusy">Save New Photo</span>
                <span x-show="refreshBusy">Saving...</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- TOASTS -->
    <div class="toast-wrap" id="toast-wrap"></div>
  </div>

  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script>
    function adminApp(){
      const EP = {
        pending:  @json(route('students.pending')),
        approved: @json(route('students.approved')),
        preview:  @json(route('students.preview')),
        approve:  @json(route('students.approve')),
        decline:  @json(route('students.decline')),
        refresh:  @json(route('students.refreshPhoto')),
        logout:   @json(route('logout')),
        enrolledImport: @json(route('enrolled.import')),
        enrolledCount:  @json(route('enrolled.count')),
        faculty: {
          list:    @json(route('faculty.list')),
          save:    @json(route('faculty.store')),
          preview: @json(route('faculty.preview')),
          print:   @json(route('faculty.print')),
          delete:  @json(route('faculty.delete')),
        }
      };
      const currentUser = @json(session('portal_user'));

      // small toast helper
      function toast(type, title, msg){
        const w = document.getElementById('toast-wrap');
        const el = document.createElement('div');
        el.className = 'toast ' + (type==='err' ? 'err' : 'ok');
        el.innerHTML = `<div><strong>${title}</strong><div style="font-size:12px">${msg??''}</div></div>
                        <button class="close">&times;</button>`;
        w.appendChild(el);
        requestAnimationFrame(()=>el.classList.add('show'));
        const close = ()=>{el.classList.remove('show'); setTimeout(()=>el.remove(),180);}
        el.querySelector('.close').onclick = close;
        setTimeout(close, 3500);
      }

      // upgrade plain text "Preview/Approve/Decline" into real buttons (works with old partials)
      function upgradeActionCells(tbody){
        [...tbody.querySelectorAll('td:last-child')].forEach(cell=>{
          if(cell.querySelector('[data-action]')) return; // already buttons
          const txt = cell.textContent.trim();
          if(!txt) return;

          // Build button row
          const wrap = document.createElement('div');
          wrap.style.display = 'flex';
          wrap.style.gap = '8px';
          wrap.style.alignItems = 'center';

          // Try to detect an id from the row dataset or first cell fallback
          const tr = cell.closest('tr');
          const id = tr?.dataset?.id || tr?.querySelector('td')?.textContent?.trim() || '';

          const mk = (label, klass, action)=> {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = klass;
            b.dataset.action = action;
            b.dataset.id = tr?.dataset?.rowId || tr?.getAttribute('data-row-id') || (tr?.dataset?.id || id);
            b.textContent = label;
            return b;
          };

          wrap.appendChild(mk('Preview','chip chip-soft','preview'));
          wrap.appendChild(mk('Approve','chip chip-emerald','approve'));
          wrap.appendChild(mk('Decline','chip chip-outline','decline'));

          cell.textContent = '';
          cell.appendChild(wrap);
        });
      }

      // delegate click handlers on TBODY
      function wireDelegation(tbody, handlers){
        if(!tbody || tbody._wired) return;
        tbody.addEventListener('click', (e)=>{
          const btn = e.target.closest('[data-action]');
          if(!btn) return;
          const action = btn.dataset.action;
          let id = btn.dataset.id;
          if(!id){
            const tr = btn.closest('tr');
            id = tr?.dataset?.rowId || tr?.dataset?.id || tr?.dataset?.number;
          }
          handlers[action]?.(id, btn);
        });
        tbody._wired = true;
      }

      // CSRF post helper
      async function post(url, body){
        const res = await fetch(url, {
          method:'POST',
          headers:{
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body
        });
        const t = await res.text();
        try { return JSON.parse(t); } catch { return t; }
      }

      const blankFaculty = () => ({
        id: null,
        first_name: '',
        middle_initial: '',
        last_name: '',
        name: '',
        position: '',
        gender: '',
        blood_type: '',
        address: '',
        emergency_contact_address: '',
        emergency_contact_name: '',
        emergency_contact_number: '',
        civil_status: '',
        gsis_number: '',
        sss_number: '',
        tin_number: '',
        birthday: '',
        id_path: '',
      });

      return {
        tab: 'dashboard',
        currentUser,
        defaultAvatar: @json(asset('images/CvSU-navbar-Logo-PNG.png')),
        profileMenuOpen: false,
        navOpen: window.innerWidth >= 960,
        isMobile: window.innerWidth < 960,
        updatedAt: '',
        facultyUpdatedAt: '',
        rosterStatus: '',
        rosterError: '',
        rosterBusy: false,
        rosterReplace: true,
        rosterFileName: '',
        settingsTab: 'account',
        accountName: currentUser?.name || '',
        accountFirst: (currentUser?.name || '').split(' ')[0] || '',
        accountLast: (currentUser?.name || '').split(' ').slice(1).join(' ') || '',
        accountUsername: currentUser?.email || currentUser?.student_number || '',
        accountPassCurrent: '',
        accountPassNew: '',
        accountPhotoName: '',
        accountPhotoUrl: currentUser?.photo_url || '',
        accountLocked: true,
        accountStatus: '',
        accountError: '',
        accountBusy: false,
        isDarkMode: false,
        themeResetting: false,
        modalOpen: false,
        modalStudent: null,
        modalCardHtml: '',
        modalLoading: false,
        modalError: '',
        refreshOpen: false,
        refreshStudent: null,
        refreshStatus: '',
        refreshError: '',
        refreshVector: null,
        refreshBusy: false,
        sel: { pending: [], approved: [], faculty: [] },
        counts: { pending: 0, approved: 0, faculty: 0, enrolled: 0 },
        facultyForm: blankFaculty(),
        facultyStep: 0,
        facultyPhotoUrl: '',
        facultyConfirm: false,
        facultyPreview: null,
        facultyCardHtml: '',
        facultyModalOpen: false,
        facultyModalLoading: false,
        facultyModalError: '',
        facultyModalCardHtml: '',
        facultySaving: false,
        facultyError: '',
        facultySuccess: '',

        async boot(){
          // theme
          const storedTheme = localStorage.getItem('admin-theme');
          if(storedTheme === 'dark'){ this.isDarkMode = true; }
          if(storedTheme === 'light'){ this.isDarkMode = false; }
          this.applyTheme();
          this.handleResize();
          window.addEventListener('resize', () => this.handleResize());

          await this.loadTables();
          await this.loadFacultyTable();
          await this.loadEnrolledCount();
          this.updatedAt = new Date().toLocaleTimeString();
        },

        async loadTables(){
          // Pending
          try{
            const html = await (await fetch(EP.pending, {cache:'no-store'})).text();
          const tbody = document.getElementById('pending-body');
          tbody.innerHTML = html;
          upgradeActionCells(tbody);
          wireDelegation(tbody, {
            preview: (id, btn) => this.openPreview(id || btn?.dataset?.id),
            approve: (id, btn) => this.confirmAction('approve', id, { name: btn?.dataset?.name, number: btn?.dataset?.number }),
            decline: (id, btn) => this.confirmAction('decline', id, { name: btn?.dataset?.name, number: btn?.dataset?.number }),
          });
          this.counts.pending = tbody.querySelectorAll('tr[data-row-id]').length;
          this.sel.pending = [];
          }catch(e){
            document.getElementById('pending-body').innerHTML =
              `<tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Error loading pending.</td></tr>`;
            this.counts.pending = 0;
          }

          // Approved
          try{
            const html = await (await fetch(EP.approved, {cache:'no-store'})).text();
          const tbody = document.getElementById('approved-body');
          tbody.innerHTML = html;
          upgradeActionCells(tbody); // gives preview button
          wireDelegation(tbody, {
            preview: (id, btn) => this.openPreview(id || btn?.dataset?.id),
            refresh: (id, btn) => this.openRefresh(id, btn),
          });
          tbody.querySelectorAll('input.row-select').forEach(cb=>{
            cb.addEventListener('change', (e)=>{
              const id = e.target.getAttribute('data-id');
              this.toggleOne('approved', id, e.target.checked);
            });
          });
          this.counts.approved = tbody.querySelectorAll('tr[data-row-id]').length;
          }catch(e){
            document.getElementById('approved-body').innerHTML =
              `<tr><td colspan="5" style="padding:16px;text-align:center;color:var(--muted)">Error loading approved.</td></tr>`;
            this.counts.approved = 0;
          }
        },

        async loadFacultyTable(){
          try{
            const html = await (await fetch(EP.faculty.list, { cache:'no-store' })).text();
            const tbody = document.getElementById('faculty-body');
            tbody.innerHTML = html;
            upgradeActionCells(tbody);
            wireDelegation(tbody, {
              preview: (id, btn) => this.openFacultyPreview(id || btn?.dataset?.id, true),
              edit: (id, btn) => this.editFaculty(id || btn?.dataset?.id),
              delete: (id, btn) => this.deleteFaculty(id || btn?.dataset?.id),
            });
            tbody.querySelectorAll('input.row-select').forEach(cb=>{
              cb.addEventListener('change', (e)=>{
                const id = e.target.getAttribute('data-id');
                this.toggleOne('faculty', id, e.target.checked);
              });
            });
            this.counts.faculty = tbody.querySelectorAll('tr[data-row-id]').length;
            this.sel.faculty = [];
            this.facultyUpdatedAt = new Date().toLocaleTimeString();
          }catch(e){
            document.getElementById('faculty-body').innerHTML =
            `<tr><td colspan="7" style="padding:16px;text-align:center;color:var(--muted)">Error loading faculty.</td></tr>`;
            this.counts.faculty = 0;
          }
        },

        async loadEnrolledCount(){
          try{
            const res = await fetch(EP.enrolledCount, { cache:'no-store' });
            const text = await res.text();
            let payload = null;
            try { payload = JSON.parse(text); } catch {}
            if(!res.ok || payload?.status !== 'success'){
              this.counts.enrolled = 0;
              return;
            }
            this.counts.enrolled = Number(payload.count || 0);
          }catch(e){
            this.counts.enrolled = 0;
          }
        },

        async submitRoster(){
          this.rosterError = '';
          this.rosterStatus = '';
          const input = this.$refs?.rosterFile;
          const file = input?.files?.[0];
          if(!file){
            this.rosterError = 'Choose an .xlsx file to upload.';
            return;
          }
          if(!file.name.toLowerCase().endsWith('.xlsx')){
            this.rosterError = 'Only .xlsx files are accepted.';
            return;
          }

          this.rosterBusy = true;
          const fd = new FormData();
          fd.append('roster', file);
          fd.append('replace_existing', this.rosterReplace ? '1' : '0');

          try{
            const res = await fetch(EP.enrolledImport, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
              body: fd,
            });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch(e){ payload = null; }

            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message || 'Upload failed. Check your file and headers.';
              this.rosterError = msg;
              toast('err', 'Upload failed', msg);
              return;
            }

            const msg = payload?.message || 'Roster uploaded.';
            this.rosterStatus = msg;
            this.rosterError = '';
            this.rosterFileName = '';
            if(input){ input.value = ''; }
            toast('ok', 'Roster imported', msg);
            await this.loadEnrolledCount();
          }catch(err){
            console.error(err);
            this.rosterError = 'Unexpected error while uploading roster.';
            toast('err', 'Upload failed', 'Unexpected error.');
          }finally{
            this.rosterBusy = false;
          }
        },

        handleAccountPhoto(ev){
          const file = ev.target?.files?.[0];
          this.accountPhotoName = file?.name || '';
          if(file){
            this.accountPhotoUrl = URL.createObjectURL(file);
          }else{
            this.accountPhotoUrl = currentUser?.photo_url || '';
          }
        },

        saveAccount(){
          this.accountError = '';
          this.accountStatus = '';
          if(!this.accountName){
            this.accountError = 'Display name is required.';
            return;
          }
          this.accountBusy = true;
          // Placeholder: backend endpoint not implemented yet
          setTimeout(()=>{
            this.accountBusy = false;
            this.accountStatus = 'Profile changes queued (not yet wired to backend).';
            // keep showing chosen avatar locally
            toast('ok', 'Saved', 'Profile changes recorded locally.');
            this.accountLocked = true;
          }, 400);
        },

        resetAccount(){
          this.accountFirst = (currentUser?.name || '').split(' ')[0] || '';
          this.accountLast = (currentUser?.name || '').split(' ').slice(1).join(' ') || '';
          this.accountName = currentUser?.name || '';
          this.accountUsername = currentUser?.email || currentUser?.student_number || '';
          this.accountPassCurrent = '';
          this.accountPassNew = '';
          this.accountPhotoName = '';
          this.accountPhotoUrl = currentUser?.photo_url || '';
          if(this.$refs?.accountPhoto){ this.$refs.accountPhoto.value=''; }
          this.accountError = '';
          this.accountStatus = '';
          this.accountLocked = true;
        },

        openSettingsTab(which){
          this.tab = 'settings';
          this.settingsTab = which || 'account';
          this.profileMenuOpen = false;
          document.querySelectorAll('.navbtn').forEach(b=>b.classList.remove('active'));
          const settingsBtn = document.querySelector('[data-nav="settings"]');
          if(settingsBtn){ settingsBtn.classList.add('active'); }
        },

        applyTheme(){
          document.body.classList.toggle('theme-dark', !!this.isDarkMode);
          localStorage.setItem('admin-theme', this.isDarkMode ? 'dark' : 'light');
        },

        toggleTheme(){
          this.applyTheme();
        },

        resetTheme(){
          this.themeResetting = true;
          localStorage.removeItem('admin-theme');
          // follow system preference
          const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
          this.isDarkMode = !!prefersDark;
          this.applyTheme();
          setTimeout(()=>{ this.themeResetting = false; }, 200);
        },

        handleResize(){
          this.isMobile = window.innerWidth < 960;
          if(!this.isMobile){
            this.navOpen = true;
          }else{
            this.navOpen = false;
            this.profileMenuOpen = false;
          }
        },

        resetFacultyForm(){
          this.facultyForm = blankFaculty();
          this.facultyPhotoUrl = '';
          this.facultyStep = 0;
          this.facultyConfirm = false;
          this.facultyError = '';
          this.facultySuccess = '';
        },

        canProceed(step){
          const filled = (v) => (v ?? '').toString().trim().length > 0;
          if(step === 0){
            const fields = ['first_name','middle_initial','last_name','position','gender','blood_type','civil_status','birthday'];
            return fields.every(f => filled(this.facultyForm[f]));
          }
          if(step === 1){
            const fields = ['emergency_contact_name','emergency_contact_number','emergency_contact_address','gsis_number','sss_number','tin_number'];
            const hasPhoto = !!(this.$refs?.facultyPhoto?.files?.length || this.facultyPhotoUrl);
            return fields.every(f => filled(this.facultyForm[f])) && hasPhoto;
          }
          if(step === 2){
            return !!this.facultyConfirm;
          }
          return true;
        },

        goNext(){
          if(!this.canProceed(this.facultyStep)){
            this.facultyError = 'Please complete all fields before continuing.';
            return;
          }
          this.facultyError = '';
          this.facultyStep = Math.min(2, this.facultyStep + 1);
        },

        goBack(){
          this.facultyError = '';
          this.facultyStep = Math.max(0, this.facultyStep - 1);
        },

        coveragePercent(){
          const enrolled = Number(this.counts.enrolled) || 0;
          const approved = Number(this.counts.approved) || 0;
          if(!enrolled) return 0;
          return Math.min(100, Math.round((approved / enrolled) * 100));
        },

        async submitFaculty(){
          if(this.facultyStep !== 2){
            this.facultyStep = 2;
            this.facultyError = 'Please move to Step 3 and confirm details before saving.';
            return;
          }
          if(!this.facultyConfirm){
            this.facultyError = 'Check the confirmation box before saving.';
            return;
          }
          this.facultySaving = true;
          this.facultyError = '';
          this.facultySuccess = '';

          const fd = new FormData();
          Object.entries(this.facultyForm).forEach(([k,v])=>{
            if(k === 'id' && (!v || v === 'null' || v === '')) return;
            fd.append(k, v ?? '');
          });
          const photoFile = this.$refs?.facultyPhoto?.files?.[0];
          if(photoFile){ fd.append('photo', photoFile); }

          try{
            const res = await fetch(EP.faculty.save, {
              method:'POST',
              headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
              body: fd
            });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch { payload = null; }

            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message
                || (payload?.errors ? Object.values(payload.errors).flat()[0] : 'Failed to save faculty record.');
              this.facultyError = msg;
              toast('err', 'Save failed', msg);
              return;
            }

            this.facultyPreview = payload.faculty;
            this.facultyCardHtml = payload.card_preview || '';
            this.facultyForm = { ...this.facultyForm, ...payload.faculty, id: payload.faculty?.id ?? null };
            this.facultyPhotoUrl = payload.faculty?.photo_url || '';
            this.facultySuccess = 'Saved. Preview updated from the database.';
            this.facultyStep = 2;
            toast('ok', 'Faculty saved', 'Preview ready for print.');
            await this.loadFacultyTable();
          }catch(err){
            console.error(err);
            this.facultyError = 'Unexpected error while saving.';
            toast('err', 'Save failed', 'Unexpected error.');
          }finally{
            this.facultySaving = false;
          }
        },

        async refreshFacultyPreview(){
          const id = this.facultyPreview?.id || this.facultyForm?.id;
          if(!id){
            this.facultyError = 'Save the record first to load a preview.';
            return;
          }
          try{
            const res = await fetch(EP.faculty.preview + '?id=' + encodeURIComponent(id), { cache: 'no-store' });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch { payload = null; }
            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message || 'Failed to load preview.';
              this.facultyError = msg;
              toast('err', 'Preview', msg);
              return;
            }
            this.facultyPreview = payload.faculty;
            this.facultyCardHtml = payload.card_preview || '';
            this.facultyForm = { ...this.facultyForm, ...payload.faculty, id: payload.faculty?.id ?? null };
            this.facultySuccess = 'Preview loaded from the database.';
            this.facultyError = '';
            toast('ok', 'Preview updated', 'Reloaded from database');
          }catch(err){
            console.error(err);
            this.facultyError = 'Unexpected error while loading preview.';
            toast('err', 'Preview failed', 'Unexpected error.');
          }
        },

        async openFacultyPreview(id, showModal = false){
          if(!id) return;
          this.facultyModalOpen = !!showModal;
          if(showModal){
            this.facultyModalLoading = true;
            this.facultyModalError = '';
            this.facultyModalCardHtml = '';
          }
          try{
            const res = await fetch(EP.faculty.preview + '?id=' + encodeURIComponent(id) + '&format=json', { cache: 'no-store' });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch { payload = null; }
            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message || 'Failed to load preview.';
              if(showModal){
                this.facultyModalError = msg;
                this.facultyModalLoading = false;
              }
              toast('err', 'Preview', msg);
              return;
            }
            this.facultyPreview = payload.faculty;
            this.facultyCardHtml = payload.card_preview || '';
            if(showModal){
              this.facultyModalCardHtml = payload.card_preview || '';
              this.facultyModalLoading = false;
            }else{
              this.facultyForm = { ...blankFaculty(), ...payload.faculty, id: payload.faculty?.id ?? null };
              this.facultyPhotoUrl = payload.faculty?.photo_url || '';
              this.facultySuccess = 'Preview loaded from the database.';
              this.facultyError = '';
              this.tab = 'faculty';
              this.facultyStep = 2;
            }
          }catch(err){
            console.error(err);
            this.facultyError = 'Unexpected error while loading preview.';
            toast('err', 'Preview failed', 'Unexpected error.');
          }
        },

        async editFaculty(id){
          await this.openFacultyPreview(id);
        },

        async deleteFaculty(id){
          if(!id) return;
          if(!confirm('Delete this faculty record?')) return;
          try{
            const res = await fetch(EP.faculty.delete, {
              method:'POST',
              headers:{
                'Content-Type':'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body:'id=' + encodeURIComponent(id)
            });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch { payload = null; }
            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message || 'Failed to delete.';
              toast('err', 'Delete failed', msg);
              return;
            }
            toast('ok', 'Deleted', 'Faculty removed.');
            await this.loadFacultyTable();
          }catch(err){
            console.error(err);
            toast('err', 'Delete failed', 'Unexpected error.');
          }
        },

        printFaculty(){
          const id = this.facultyPreview?.id || this.facultyForm?.id;
          if(!id){
            this.facultyError = 'Save the faculty record before printing.';
            return;
          }
          const url = EP.faculty.print + '?id=' + encodeURIComponent(id);
          const pop = window.open(url, '_blank');
          if(pop){
            toast('ok', 'Print ready', 'A PDF opened in a new tab.');
          }else{
            alert('Popup blocked. Please allow popups for this site to continue.');
          }
        },

        toggleOne(which, id, checked){
          const arr = this.sel[which];
          const idx = arr.indexOf(id);
          if(checked && idx===-1) arr.push(id);
          if(!checked && idx>-1) arr.splice(idx,1);
        },

        toggleAll(which, ev){
          let tbody;
          if(which==='pending'){ tbody = document.getElementById('pending-body'); }
          else if(which==='approved'){ tbody = document.getElementById('approved-body'); }
          else { tbody = document.getElementById('faculty-body'); }
          const boxes = [...tbody.querySelectorAll('input.row-select')];
          const checked = !!ev.target.checked;
          boxes.forEach(b=>{ b.checked = checked; const id=b.getAttribute('data-id'); this.toggleOne(which, id, checked); });
        },

        printSelected(which){
          const ids = this.sel[which];
          if(!ids.length) return;

          if(which === 'pending'){
            toast('err', 'Printing blocked', 'Approve the student record before printing an ID.');
            return;
          }

          if(which === 'approved'){
            const proceed = confirm(`Open printable IDs for ${ids.length} approved student${ids.length>1?'s':''}?`);
            if(!proceed) return;
          }

          const url = @json(route('students.print')) + '?ids=' + encodeURIComponent(ids.join(','));
          const pop = window.open(url, '_blank');
          if(pop){
            toast('ok', 'Print ready', 'A PDF opened in a new tab.');
          }else{
            alert('Popup blocked. Please allow popups for this site to continue.');
          }
        },

        printSelectedFaculty(){
          const ids = this.sel.faculty;
          if(!ids.length){
            toast('err', 'Printing blocked', 'Select at least one faculty record.');
            return;
          }
          const proceed = confirm(`Open printable IDs for ${ids.length} faculty member${ids.length>1?'s':''}?`);
          if(!proceed) return;
          const url = EP.faculty.print + '?ids=' + encodeURIComponent(ids.join(','));
          const pop = window.open(url, '_blank');
          if(pop){
            toast('ok', 'Print ready', 'A PDF opened in a new tab.');
          }else{
            alert('Popup blocked. Please allow popups for this site to continue.');
          }
        },

        async openPreview(id){
          this.modalOpen = true;
          this.modalLoading = true;
          this.modalError = '';
          this.modalStudent = null;
          this.modalCardHtml = '';

          const url = EP.preview + '?id=' + encodeURIComponent(id) + '&format=json&t=' + Date.now();
          try{
            const res = await fetch(url, { headers:{ 'Accept':'application/json' }, cache:'no-store' });
            const text = await res.text();
            let payload = null;
            try{
              payload = JSON.parse(text);
            }catch(parseErr){
              throw new Error(text || 'Failed to load preview.');
            }

            if(!res.ok || payload?.status !== 'success'){
              throw new Error(payload?.message || 'Failed to load preview.');
            }

            const student = payload.student || {};
            student.id = student.id ?? id;
            student.full_name = [student.first_name, student.middle_initial, student.last_name]
              .filter(Boolean)
              .join(' ')
              .replace(/\s+/g,' ')
              .trim();
            if(student.status){
              student.status_label = student.status.charAt(0).toUpperCase() + student.status.slice(1);
            }

            this.modalStudent = student;
            this.modalCardHtml = payload.card_preview || '';
            this.modalLoading = false;
          }catch(e){
            console.error(e);
            this.modalError = e?.message || 'Failed to load preview.';
            this.modalLoading = false;
          }
        },

        closeModal(){
          this.modalOpen = false;
          this.modalStudent = null;
          this.modalCardHtml = '';
          this.modalError = '';
          this.modalLoading = false;
        },

        openRefresh(id, btn){
          const tr = btn?.closest?.('tr') || document.querySelector(`tr[data-row-id="${CSS.escape(id)}"]`);
          const nameFromRow = tr ? [tr?.dataset?.first, tr?.dataset?.last].filter(Boolean).join(' ') : '';
          this.refreshStudent = {
            id,
            number: btn?.dataset?.number || tr?.dataset?.number || id,
            name: btn?.dataset?.name || nameFromRow || `Student #${id}`,
          };
          this.refreshOpen = true;
          this.refreshStatus = '';
          this.refreshError = '';
          this.refreshVector = null;
          this.refreshBusy = false;
          this.$nextTick(() => {
            if(this.$refs?.refreshFile){ this.$refs.refreshFile.value = ''; }
            if(this.$refs?.refreshEmbedding){ this.$refs.refreshEmbedding.value = ''; }
          });
        },

        closeRefresh(){
          this.refreshOpen = false;
          this.refreshStudent = null;
          this.refreshStatus = '';
          this.refreshError = '';
          this.refreshVector = null;
          if(this.$refs?.refreshFile){ this.$refs.refreshFile.value = ''; }
          if(this.$refs?.refreshEmbedding){ this.$refs.refreshEmbedding.value = ''; }
        },

        async handleRefreshFile(ev){
          this.refreshError = '';
          this.refreshStatus = '';
          this.refreshVector = null;
          if(this.$refs?.refreshEmbedding){ this.$refs.refreshEmbedding.value = ''; }

          const file = ev.target.files?.[0];
          if(!file){
            this.refreshError = 'Select an image to continue.';
            return;
          }
          const okType = ['image/jpeg','image/png'].includes(file.type);
          const okSize = file.size <= 5 * 1024 * 1024;
          if(!okType){
            this.refreshError = 'Only JPG and PNG files are allowed.';
            ev.target.value = '';
            return;
          }
          if(!okSize){
            this.refreshError = 'Max file size is 5MB.';
            ev.target.value = '';
            return;
          }

          const url = URL.createObjectURL(file);
          const img = new Image();
          img.onload = async () => {
            const square = Math.abs(img.width - img.height) <= 2;
            if(!square || img.width < 300 || img.height < 300){
              this.refreshError = 'Photo must be square (1:1) and at least 300x300px.';
              URL.revokeObjectURL(url);
              ev.target.value = '';
              return;
            }

            if(!window.FaceVerifier){
              this.refreshError = 'Face verifier script is unavailable.';
              URL.revokeObjectURL(url);
              return;
            }

            try{
              this.refreshBusy = true;
              this.refreshStatus = 'Analyzing photo...';
              await window.FaceVerifier.prepare();
              const result = await window.FaceVerifier.embedFile(file, { minConfidence: 0.5 });
              this.refreshVector = result.vector;
              if(this.$refs?.refreshEmbedding){
                this.$refs.refreshEmbedding.value = JSON.stringify(this.refreshVector);
              }
              const pct = result.overview?.confidence !== undefined
                ? ` ${(result.overview.confidence * 100).toFixed(1)}% confidence`
                : '';
              this.refreshStatus = `Face captured.${pct}`;
              this.refreshError = '';
            }catch(err){
              this.refreshVector = null;
              if(this.$refs?.refreshEmbedding){
                this.$refs.refreshEmbedding.value = ''; }
              const code = err?.message || '';
              if(code === 'face-not-found'){
                this.refreshError = 'No face detected. Try another photo.';
              }else{
                this.refreshError = 'Face verification failed. Try a clearer photo.';
                console.error(err);
              }
            }finally{
              this.refreshBusy = false;
              URL.revokeObjectURL(url);
            }
          };
          img.onerror = () => {
            this.refreshError = 'Invalid image file.';
            URL.revokeObjectURL(url);
            ev.target.value = '';
          };
          img.src = url;
        },
        async submitRefresh(){
          if(!this.refreshStudent){
            this.refreshError = 'Select a student before submitting.';
            return;
          }
          const file = this.$refs?.refreshFile?.files?.[0];
          if(!file){
            this.refreshError = 'Choose a photo to upload.';
            return;
          }
          if(!this.refreshVector){
            this.refreshError = 'Run face verification on the new photo before saving.';
            return;
          }

          this.refreshBusy = true;
          this.refreshError = '';

          const fd = new FormData();
          fd.append('student_id', this.refreshStudent.id);
          fd.append('photo', file);
          fd.append('face_embedding', JSON.stringify(this.refreshVector));

          try{
            const res = await fetch(EP.refresh, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
              body: fd,
            });
            const text = await res.text();
            let payload;
            try { payload = JSON.parse(text); } catch { payload = { message: text }; }
            if(!res.ok || payload?.status !== 'success'){
              const msg = payload?.message || 'Failed to update photo.';
              this.refreshError = msg;
              toast('err', 'Update failed', msg);
              return;
            }
            toast('ok', 'Reference updated', `Saved new photo for ${this.refreshStudent.number}`);
            this.closeRefresh();
            await this.loadTables();
            this.updatedAt = new Date().toLocaleTimeString();
          }catch(err){
            console.error(err);
            this.refreshError = 'Unexpected error while updating photo.';
            toast('err', 'Update failed', 'Unexpected error.');
          }finally{
            this.refreshBusy = false;
          }
        },

        async confirmAction(kind, id, meta = {}){
          if(!id) return;
          const verb = kind==='approve' ? 'Approve' : 'Decline';
          const descriptor = meta.name
            ? `${meta.name}${meta.number ? ' - ' + meta.number : ''}`
            : (meta.number ? `Student #${meta.number}` : 'this student');

          if(!confirm(`${verb} ${descriptor}?`)) return;

          toast('ok', verb, 'Processing...');
          const res = await post(EP[kind], 'id=' + encodeURIComponent(id));
          const ok = (typeof res==='string' && res.trim()==='success') || (res?.status==='success');

          if(ok){
            toast('ok', verb, `${descriptor} updated`);
            await this.loadTables();
            this.updatedAt = new Date().toLocaleTimeString();
            await this.loadFacultyTable();
            this.closeModal();
          }else{
            const msg = typeof res==='string' ? res : (res?.message || 'Unknown error');
            toast('err', verb + ' failed', msg);
          }
        },

        filterTable(which, query){
          query = (query||'').toLowerCase();
          let tbody;
          if(which==='pending'){ tbody = document.getElementById('pending-body'); }
          else if(which==='approved'){ tbody = document.getElementById('approved-body'); }
          else { tbody = document.getElementById('faculty-body'); }
          [...tbody.querySelectorAll('tr')].forEach(tr=>{
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(query) ? '' : 'none';
          });
        },

        show(which, ev){
          this.tab = which;
          document.querySelectorAll('.navbtn').forEach(b=>b.classList.remove('active'));
          if(ev?.currentTarget?.classList?.contains('navbtn')){
            ev.currentTarget.classList.add('active');
          }else{
            const navBtn = document.querySelector(`[data-nav="${CSS.escape(which)}"]`);
            if(navBtn){ navBtn.classList.add('active'); }
          }
          this.profileMenuOpen = false;
        },

        async logout(){
          if(!confirm('Logout from admin?')) return;
          this.profileMenuOpen = false;
          await post(EP.logout, '');
          location.href = @json(url('/'));
        }
      }
    }
  </script>
  <script>
    // Simple dropdown toggle for action menus on faculty list
    document.addEventListener('click', (e)=>{
      const menuBtn = e.target.closest('[data-menu]');
      const menus = document.querySelectorAll('[data-menu-target]');
      // hide all
      menus.forEach(m => m.style.display = 'none');
      if(menuBtn){
        const id = menuBtn.getAttribute('data-menu');
        const menu = document.querySelector('[data-menu-target="'+id+'"]');
        if(menu){
          menu.style.display = 'block';
          e.stopPropagation();
        }
      }
    });
  </script>
</body>
</html>
