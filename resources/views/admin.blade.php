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
      <div class="kicker">&bull; Secure session</div>
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
              <input class="search" style="flex:1 1 260px" placeholder="Search pending..." @input="filterTable('pending', $event.target.value)">
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
              <input class="search" style="flex:1 1 260px" placeholder="Search students..." @input="filterTable('approved', $event.target.value)">
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
                  <tr><td colspan="6" style="padding:16px;text-align:center;color:var(--muted)">Loading...</td></tr>
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
              <label style="display:block;font-size:13px;font-weight:600;color:#0f172a">New 1x1 Photo</label>
              <input type="file" x-ref="refreshFile" accept="image/jpeg,image/png" @change="handleRefreshFile($event)"
                     style="margin-top:6px;width:100%;padding:10px;border:1px dashed var(--ring);border-radius:10px;background:#fff">
              <input type="hidden" x-ref="refreshEmbedding">
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
      };

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

      return {
        tab: 'pending',
        updatedAt: '',
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







