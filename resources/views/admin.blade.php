<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard — ID Registration Portal</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">

  <style>
    :root{
      --emerald:#059669;
      --emerald-700:#047857;
      --rose:#E11D48;
      --ink:#0f172a;
      --muted:#64748b;
      --panel:#ffffff;
      --bg:#f6fbf8;
      --ring:#e2e8f0;
      --radius:14px;
    }
    *{box-sizing:border-box;margin:0;padding:0;font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial;}
    body{background:var(--bg);color:var(--ink);min-height:100vh;}

    /* Shell */
    .wrap{max-width:1280px;margin:0 auto;padding:18px;}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin:8px 0 14px;}
    .brand{display:flex;align-items:center;gap:10px;}
    .logo{height:36px;width:36px;border-radius:999px;background:var(--emerald);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;}
    .kicker{font-size:12px;color:var(--muted);}

    .grid{display:grid;grid-template-columns:280px 1fr;gap:18px}
    @media (max-width:980px){ .grid{grid-template-columns:1fr} }

    /* Sidebar */
    .side{background:var(--panel);border:1px solid var(--ring);border-radius:var(--radius);padding:14px;}
    .sidetop{display:flex;align-items:center;gap:10px;margin-bottom:10px}
    .nav{display:flex;flex-direction:column;gap:8px;margin-top:10px}
    .navbtn{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--ring);background:#fff;cursor:pointer;font-weight:600;position:relative}
    .navbtn.active{background:#ecfdf5;border-color:#a7f3d0;color:#065f46}
    .navbtn .notify-dot{
      margin-left:auto;
      min-width:26px;
      padding:0 8px;
      height:22px;
      border-radius:999px;
      background:#f43f5e;
      color:#fff;
      font-size:11px;
      font-weight:800;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      box-shadow:0 0 0 2px #fff;
    }
    .logout{display:block;margin-top:12px;width:100%;padding:10px;border-radius:10px;background:#ef4444;color:#fff;border:none;font-weight:700;cursor:pointer}

    /* Card */
    .card{background:var(--panel);border:1px solid var(--ring);border-radius:var(--radius);overflow:hidden}
    .card-hd{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid var(--ring)}
    .card-hd h2{font-size:16px}
    .card-sub{font-size:12px;color:var(--muted)}
    .card-bd{padding:16px}

    /* Search */
    .search{width:320px;max-width:100%;padding:10px 12px;border-radius:10px;border:1px solid var(--ring);outline:none}
    .meta{font-size:12px;color:var(--muted);}

    /* Table */
    table{width:100%;border-collapse:collapse}
    thead th{font-size:12px;text-align:left;color:#064e3b;background:#ecfdf5;border-bottom:1px solid var(--ring);padding:10px}
    tbody td{padding:10px;border-bottom:1px solid var(--ring)}
    tbody tr:hover td{background:#fafafa}

    /* Buttons */
    .chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;line-height:1;border-radius:999px;padding:8px 12px;border:1px solid transparent;cursor:pointer;transition:.15s}
    .chip-emerald{background:var(--emerald);color:#fff}.chip-emerald:hover{background:var(--emerald-700)}
    .chip-outline{background:#fff;border-color:#fecdd3;color:#be123c}.chip-outline:hover{background:#fff0f3}
    .chip-soft{background:#eef2ff;color:#3730a3}.chip-soft:hover{background:#e0e7ff}

    .helper-pill{display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:999px;background:#f8fafc;border:1px solid var(--ring);color:var(--muted);font-size:12px;line-height:1.2}
    .count-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;font-size:12px;font-weight:700;line-height:1}

    /* Modal */
    .backdrop{position:fixed;inset:0;background:rgba(2,6,23,.55);display:none;align-items:center;justify-content:center;z-index:50}
    .backdrop.show{display:flex}
    .modal{width:min(920px,96vw);max-height:90vh;overflow:auto;background:#fff;border-radius:16px;border:1px solid var(--ring);box-shadow:0 30px 80px rgba(2,6,23,.25);transform:translateY(12px);opacity:0;transition:.18s}
    .backdrop.show .modal{transform:translateY(0);opacity:1}
    .modal-hd{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--ring)}
    .modal-bd{padding:14px}
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
  </style>
</head>
<body>

  <div class="wrap" x-data="adminApp()" x-init="boot()">
    <!-- top -->
    <div class="topbar">
      <div class="brand">
        <div class="logo">ID</div>
        <div>
          <div style="font-weight:800">Admin Dashboard</div>
          <div class="kicker">Manage student ID registrations</div>
        </div>
      </div>
      <div class="kicker">● Secure session</div>
    </div>

    <div class="grid">
      <!-- sidebar -->
      <aside class="side">
        <div class="sidetop">
          <img src="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}" alt="" style="height:34px;width:34px;border-radius:8px;background:#fff;padding:6px;border:1px solid var(--ring)">
          <div>
            <div style="font-weight:700">Admin</div>
            <div class="kicker">ID Registration Portal</div>
          </div>
        </div>

        <nav class="nav">
          <button class="navbtn active" @click="show('pending',$event)">
            <span>Pending Approvals</span>
            <span class="notify-dot" x-show="counts.pending>0" x-text="counts.pending" style="display:none;"></span>
          </button>
          <button class="navbtn" @click="show('approved',$event)">Registered Students</button>
          <button class="navbtn" @click="show('settings',$event)">Settings</button>
        </nav>

        <button class="logout" @click="logout()">Logout</button>
      </aside>

      <!-- content -->
      <main class="grid-col">

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
              <input class="search" style="flex:1 1 260px" placeholder="Search pending…" @input="filterTable('pending', $event.target.value)">
            </div>

            <div style="overflow:auto">
              <table id="pendingTable">
                <thead>
                  <tr>
                    <th>Student #</th><th>First</th><th>Last</th><th>Course</th><th>Date</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="pending-body">
                  <tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Loading…</td></tr>
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
              <input class="search" style="flex:1 1 260px" placeholder="Search students…" @input="filterTable('approved', $event.target.value)">
              <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span class="count-pill" x-show="sel.approved.length>0" x-text="sel.approved.length + (sel.approved.length===1 ? ' student selected' : ' students selected')"></span>
                <button class="chip chip-emerald" :disabled="sel.approved.length===0" @click="printSelected('approved')">Print IDs</button>
              </div>
            </div>

            <div style="overflow:auto">
              <table id="approvedTable">
                <thead>
                  <tr>
                    <th><input type="checkbox" @change="toggleAll('approved',$event)"></th>
                    <th>Student #</th><th>First</th><th>Last</th><th>Course</th><th>Actions</th>
                  </tr>
                </thead>
                <tbody id="approved-body">
                  <tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Loading…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>

        <!-- Settings (placeholder) -->
        <section class="card" x-show="tab==='settings'" x-transition.opacity>
          <div class="card-hd"><h2>Settings</h2></div>
          <div class="card-bd">
            <div class="card-sub">Update registration period, admin credentials, or manage user roles.</div>
          </div>
        </section>

      </main>
    </div>

    <!-- MODAL -->
    <div class="backdrop" :class="{show:modalOpen}" @click.self="closeModal()" @keydown.escape.window="closeModal()">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="pv-title">
        <div class="modal-hd">
          <div id="pv-title" style="font-weight:800">Student Preview</div>
          <button class="xbtn" @click="closeModal()">Close</button>
        </div>
        <div class="modal-bd" x-html="modalHTML">Loading…</div>
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
        logout:   @json(route('logout')),
      };

      // small toast helper
      function toast(type, title, msg){
        const w = document.getElementById('toast-wrap');
        const el = document.createElement('div');
        el.className = 'toast ' + (type==='err' ? 'err' : 'ok');
        el.innerHTML = `<div><strong>${title}</strong><div style="font-size:12px">${msg??''}</div></div>
                        <button class="close">✕</button>`;
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
          const id = btn.dataset.id;
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

      return {
        tab: 'pending',
        updatedAt: '',
        modalOpen: false,
        modalHTML: '',
        sel: { pending: [], approved: [] },
        counts: { pending: 0, approved: 0 },

        async boot(){
          await this.loadTables();
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
            preview: id => this.openPreview(id),
            approve: id => this.confirmAction('approve', id),
            decline: id => this.confirmAction('decline', id),
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
          wireDelegation(tbody, { preview: id => this.openPreview(id) });
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

        toggleOne(which, id, checked){
          const arr = this.sel[which];
          const idx = arr.indexOf(id);
          if(checked && idx===-1) arr.push(id);
          if(!checked && idx>-1) arr.splice(idx,1);
        },

        toggleAll(which, ev){
          const tbody = document.getElementById(which==='pending' ? 'pending-body' : 'approved-body');
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

        async openPreview(id){
          this.modalOpen = true;
          this.modalHTML = '<div class="meta">Loading preview…</div>';
          try{
            // Ask JSON/HTML; controller can return HTML string or JSON
            const res = await fetch(EP.preview + '?id=' + encodeURIComponent(id) + '&t=' + Date.now(), {headers:{'Accept':'text/html,application/json'}, cache: 'no-store'});
            const text = await res.text();
            this.modalHTML = text || '<div class="meta">No preview available.</div>';
          }catch(e){
            this.modalHTML = '<div class="meta">Failed to load preview.</div>';
          }
        },

        closeModal(){ this.modalOpen = false; this.modalHTML = ''; },

        async confirmAction(kind, id){
          const verb = kind==='approve' ? 'Approve' : 'Decline';
          if(!confirm(`${verb} this student?`)) return;

          // optimistic small spinner on toast
          toast('ok', verb, 'Processing…');
          const res = await post(EP[kind], 'id=' + encodeURIComponent(id));
          const ok = (typeof res==='string' && res.trim()==='success') || (res?.status==='success');

          if(ok){
            toast('ok', verb, 'Done');
            await this.loadTables();
            this.updatedAt = new Date().toLocaleTimeString();
            this.closeModal();
          }else{
            const msg = typeof res==='string' ? res : (res?.message || 'Unknown error');
            toast('err', verb + ' failed', msg);
          }
        },

        filterTable(which, query){
          query = (query||'').toLowerCase();
          const tbody = document.getElementById(which==='pending' ? 'pending-body' : 'approved-body');
          [...tbody.querySelectorAll('tr')].forEach(tr=>{
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(query) ? '' : 'none';
          });
        },

        show(which, ev){
          this.tab = which;
          document.querySelectorAll('.navbtn').forEach(b=>b.classList.remove('active'));
          ev?.currentTarget?.classList?.add('active');
        },

        async logout(){
          if(!confirm('Logout from admin?')) return;
          await post(EP.logout, '');
          location.href = @json(url('/'));
        }
      }
    }
  </script>
</body>
</html>
