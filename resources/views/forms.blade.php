<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student ID Registration</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css','resources/js/app.js'])
  <script>
    window.faceVerifierConfig = Object.assign({ modelPath: 'face-models' }, window.faceVerifierConfig || {});
  </script>
  <script src="{{ asset('js/face-verifier.js') }}"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-emerald-50/20 min-h-screen antialiased">
  <!-- Header -->
  <header class="border-b bg-white">
    <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-full bg-emerald-600 text-white grid place-items-center font-bold">ID</div>
        <div>
          <h1 class="text-base font-semibold text-slate-800">University ID Registration</h1>
          <p class="text-xs text-slate-500">Registrar's Office</p>
        </div>
      </div>
      <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500">
        <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Secure form
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-6xl px-4 py-8">
    <div x-data="wizard()" x-init="initFromServer()" class="grid lg:grid-cols-3 gap-8">
      <!-- Main card -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-md ring-1 ring-emerald-900/10 overflow-hidden">
          <!-- Progress + stepper -->
          <div class="px-6 pt-6">
            <div class="flex items-center justify-between">
              <h2 class="text-lg font-semibold text-slate-800">Student ID Registration</h2>
              <span class="text-xs text-slate-600" x-text="`Step ${step+1} of 3`"></span>
            </div>
            <div class="mt-4">
              <ol class="grid grid-cols-3 gap-3" role="list">
                <template x-for="(label,idx) in ['Info','Photo','Confirm']" :key="idx">
                  <li class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-full border-2 font-semibold flex items-center justify-center"
                         :class="step>=idx ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-slate-300 text-slate-400'">
                      <span x-text="idx+1"></span>
                    </div>
                    <span class="text-sm" :class="step>=idx ? 'text-emerald-700 font-medium' : 'text-slate-500'" x-text="label"></span>
                  </li>
                </template>
              </ol>
              <div class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-600 transition-[width] duration-300" :style="`width:${progress}%`"></div>
              </div>

              @if (session('success'))
                <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-emerald-800 text-sm">
                  {{ session('success') }}
                </div>
              @endif
              @if ($errors->any())
                <div class="mt-4 rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-rose-800 text-sm">
                  <strong class="font-semibold">Please fix the errors below.</strong>
                </div>
              @endif
            </div>
          </div>

          <!-- Page Loading Overlay -->
          <div x-cloak x-show="loading" x-transition.opacity class="fixed inset-0 z-[90] bg-white/80 backdrop-blur-sm flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
              <div class="h-12 w-12 rounded-full border-4 border-emerald-600/80 border-t-transparent animate-spin"></div>
              <p class="text-emerald-800 font-medium text-sm">Loading?</p>
            </div>
          </div>

          <!-- Form -->
          <form class="p-6 space-y-6 md:space-y-7" method="POST" action="{{ route('students.submit') }}" enctype="multipart/form-data" @submit="onSubmit($event)">
            @csrf

            <!-- STEP 1 -->
            <section x-show="step===0" x-transition.opacity.duration.200ms x-cloak>
              <div class="grid sm:grid-cols-2 gap-6">
                <div>
                  <label class="form-label" for="id_number">Student Number (9 digits) <span class="text-rose-600">*</span></label>
                  <input id="id_number" name="id_number" type="text" inputmode="numeric" pattern="\d{9}" maxlength="9"
                         x-model="form.id_number"
                         value="{{ session('portal_user.student_number') }}"
                         readonly
                         class="form-input-solid bg-slate-100 text-slate-700 cursor-default select-text @error('id_number') !border-rose-400 @enderror"
                         placeholder="e.g., 202110512">
                  @error('id_number') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <label class="form-label" for="last_name">Last Name <span class="text-rose-600">*</span></label>
                    <input id="last_name" name="last_name" type="text" x-model="form.last_name"
                           @input="setCapitalized('last_name', $event.target.value)" required
                           value="{{ old('last_name') }}" class="form-input-solid capitalize @error('last_name') !border-rose-400 @enderror"
                           placeholder="Dela Cruz">
                  @error('last_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <label class="form-label" for="first_name">First Name <span class="text-rose-600">*</span></label>
                    <input id="first_name" name="first_name" type="text" x-model="form.first_name"
                           @input="setCapitalized('first_name', $event.target.value)" required
                           value="{{ old('first_name') }}" class="form-input-solid capitalize @error('first_name') !border-rose-400 @enderror"
                           placeholder="Juan">
                  @error('first_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label" for="middle_initial">Middle Initial <span class="text-rose-600">*</span></label>
                    <input id="middle_initial" name="middle_initial" type="text" maxlength="1" x-model="form.middle_initial"
                           x-on:input="oneLetter($event)" value="{{ old('middle_initial') }}"
                           required
                           class="form-input-solid @error('middle_initial') !border-rose-400 @enderror" placeholder="M">
                  @error('middle_initial') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <span class="form-label">Gender <span class="text-rose-600">*</span></span>
                  <div class="mt-2 flex items-center gap-6">
                    <label class="radio-pill">
                        <input type="radio" name="gender" value="M" class="peer sr-only" x-model="form.gender" @checked(old('gender')==='M') required>
                      <span>Male</span>
                    </label>
                    <label class="radio-pill">
                      <input type="radio" name="gender" value="F" class="peer sr-only" x-model="form.gender" @checked(old('gender')==='F')>
                      <span>Female</span>
                    </label>
                  </div>
                </div>

                <div>
                  <label class="form-label" for="course">Course / Program <span class="text-rose-600">*</span></label>
                    <select id="course" name="course" x-model="form.course" required class="form-input-solid @error('course') !border-rose-400 @enderror">
                    <option value="">Select</option>
                    @foreach(['BS Computer Science','BS Information Technology','BS Elementary Education','BS Business Administration','BS Nursing','BS Tourism'] as $c)
                      <option value="{{ $c }}" @selected(old('course')===$c)>{{ $c }}</option>
                    @endforeach
                  </select>
                  @error('course') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <label class="form-label" for="blood_type">Blood Type <span class="text-rose-600">*</span></label>
                    <select id="blood_type" name="blood_type" x-model="form.blood_type" required class="form-input-solid @error('blood_type') !border-rose-400 @enderror">
                    <option value="">Select</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $b)
                      <option value="{{ $b }}" @selected(old('blood_type')===$b)>{{ $b }}</option>
                    @endforeach
                  </select>
                  @error('blood_type') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                  <label class="form-label" for="address">Address <span class="text-rose-600">*</span></label>
                    <textarea id="address" name="address" rows="3" x-model="form.address" @input="setCapitalized('address', $event.target.value)"
                              required class="form-input-solid capitalize @error('address') !border-rose-400 @enderror" placeholder="Street, Barangay, City, Province">{{ old('address') }}</textarea>
                  @error('address') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <label class="form-label" for="guardian_name">Parent/Guardian Name <span class="text-rose-600">*</span></label>
                    <input id="guardian_name" name="guardian_name" type="text" x-model="form.guardian_name"
                           @input="setCapitalized('guardian_name', $event.target.value)" required
                           value="{{ old('guardian_name') }}" class="form-input-solid capitalize @error('guardian_name') !border-rose-400 @enderror" placeholder="Maria Dela Cruz">
                  @error('guardian_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <div class="flex flex-wrap items-center justify-between gap-3">
                    <label class="form-label" for="parent_address">Parent/Guardian Address <span class="text-rose-600">*</span></label>
                    <label for="sameAddress" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600">
                      <input id="sameAddress" type="checkbox" x-model="sameAddress" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                      <span>Same as my address</span>
                    </label>
                  </div>
                    <textarea id="parent_address" name="parent_address" rows="3" x-model="form.parent_address"
                              @input="setCapitalized('parent_address', $event.target.value)" required
                              :readonly="sameAddress"
                              :class="sameAddress ? 'bg-slate-100 text-slate-700 cursor-text' : ''"
                              class="form-input-solid capitalize @error('parent_address') !border-rose-400 @enderror" placeholder="Parent/Guardian full address">{{ old('parent_address') }}</textarea>
                  @error('parent_address') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                  <label class="form-label" for="guardian_contact">Parent/Guardian Contact No. <span class="text-rose-600">*</span></label>
                    <input id="guardian_contact" name="guardian_contact" type="text" x-model="form.guardian_contact" x-on:input="digitsMask($event,11)" value="{{ old('guardian_contact') }}" required class="form-input-solid @error('guardian_contact') !border-rose-400 @enderror" placeholder="09171234567">
                  @error('guardian_contact') <p class="form-error">{{ $message }}</p> @enderror
                </div>
              </div>

              <div class="mt-8 flex justify-end">
                <button type="button" class="btn-primary-solid btn-sm" @click.prevent="goTo(1)">Next</button>
              </div>
            </section>

            <!-- STEP 2 -->
            <section x-show="step===1" x-transition.opacity.duration.200ms x-cloak>
              <div class="grid gap-6">
                <div>
                  <label class="form-label" for="picture_path">
                    <span class="flex items-center gap-2">
                      <span>ID Photo (1x1)</span>
                      <span class="text-rose-600">*</span>
                    </span>
                  </label>
                  <div x-data="{ dragging:false, handleDrop(e){ e.preventDefault(); this.dragging=false; const files=e.dataTransfer.files; if(files&&files[0]){ const input=$refs.file; input.files=files; input.dispatchEvent(new Event('change')); }} }"
                       @dragover.prevent="dragging=true" @dragleave="dragging=false" @drop="handleDrop($event)"
                       class="rounded-2xl border-2 border-dashed mt-2"
                       :class="dragging ? 'border-emerald-600 bg-emerald-50' : 'border-slate-300 bg-white'">
                    <div class="p-6 text-center">
                      <p class="text-sm text-slate-700">Upload JPG/PNG, minimum 300x300px.</p>
                      <input x-ref="file" id="picture_path" name="picture_path" type="file" accept="image/jpeg,image/png" required class="form-file-solid mt-4" @change="previewPhoto($event)">
                      <input type="hidden" name="face_embedding" x-ref="embedding">
                      @error('picture_path') <p class="form-error mt-2">{{ $message }}</p> @enderror
                      <p class="form-error mt-2" x-show="imageError" x-text="imageError"></p>
                      <p class="mt-2 text-xs text-emerald-700" x-show="faceStatus" x-text="faceStatus"></p>
                      <p class="form-error mt-2" x-show="faceError" x-text="faceError"></p>
                    </div>
                  </div>
                </div>

                <div class="mt-4">
                  <div class="aspect-square w-full max-w-xs mx-auto overflow-hidden rounded-xl border-2 border-slate-300 bg-slate-50 flex items-center justify-center">
                    <template x-if="photoUrl">
                      <img :src="photoUrl" alt="Preview" class="w-full h-full object-cover cursor-zoom-in" @click="openLightbox(photoUrl)">
                    </template>
                    <template x-if="!photoUrl">
                      <div class="text-slate-400 text-sm">No photo selected</div>
                    </template>
                  </div>
                  <p class="mt-2 text-center text-xs text-emerald-700" x-show="photoUrl">Tap image to view full screen</p>
                </div>

                <div class="mt-8 flex justify-between">
                  <button type="button" class="btn-ghost-solid" @click.prevent="goTo(0)">Back</button>
                  <button type="button" class="btn-primary-solid" :disabled="!photoOk" @click.prevent="photoOk && goTo(2)">Next</button>
                </div>
              </div>
            </section>

            <!-- STEP 3 -->
            <section x-show="step===2" x-transition.opacity.duration.200ms x-cloak>
              <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 px-4 py-3">
                <p class="text-sm text-emerald-900">Review your details and photo. Tick the box to enable Submit.</p>
              </div>

              <div class="mt-6 rounded-2xl border-2 border-slate-200 bg-white">
                <div class="grid md:grid-cols-2 gap-0">
                  <dl class="divide-y divide-slate-100 [&>div:nth-child(even)]:bg-slate-50/50 rounded-xl">
                    <div class="summary-row"><dt>Student Number</dt><dd x-text="form.id_number || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Last Name</dt><dd x-text="form.last_name || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>First Name</dt><dd x-text="form.first_name || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Middle Initial</dt><dd x-text="form.middle_initial || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Gender</dt><dd x-text="form.gender==='M'?'Male':(form.gender==='F'?'Female':'N/A')"></dd></div>
                  </dl>
                  <dl class="divide-y divide-slate-100 [&>div:nth-child(even)]:bg-slate-50/50 rounded-xl">
                    <div class="summary-row"><dt>Course</dt><dd x-text="form.course || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Blood Type</dt><dd x-text="form.blood_type || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Address</dt><dd x-text="form.address || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Parent/Guardian Name</dt><dd x-text="form.guardian_name || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Parent/Guardian Address</dt><dd x-text="form.parent_address || 'N/A'"></dd></div>
                    <div class="summary-row"><dt>Guardian Contact</dt><dd x-text="form.guardian_contact || 'N/A'"></dd></div>
                  </dl>
                </div>
              </div>

              <label class="mt-6 inline-flex items-center gap-3">
                <input id="finalConfirm" name="confirm" type="checkbox" value="1" @change="confirmed = $event.target.checked" class="h-5 w-5 text-emerald-600 border-2 border-slate-300 rounded">
                <span class="text-slate-800 text-sm flex items-center gap-1">
                  I confirm all information is correct.
                  <span class="text-rose-600">*</span>
                </span>
              </label>
              @error('confirm') <p class="form-error mt-2">{{ $message }}</p> @enderror

              <div class="mt-8 flex justify-between">
                <div class="flex items-center gap-2">
                  <a href="{{ route('home') }}" class="btn-secondary-solid">Cancel</a>
                  <button type="button" class="btn-ghost-solid" @click.prevent="goTo(1)">Back</button>
                </div>
                <button type="submit" :disabled="!confirmed || submitting" class="btn-primary-solid disabled:opacity-50">
                  <span x-show="submitting" class="btn-spinner"></span>
                  <span x-text="submitting ? 'Submitting…' : 'Submit'"></span>
                </button>
              </div>
            </section>
          </form>
        </div>
      </div>

      <!-- Aside (desktop only) -->
      <aside class="hidden lg:block">
        <div class="space-y-6 lg:sticky lg:top-6">
          <div class="bg-white rounded-2xl shadow-md ring-1 ring-emerald-900/10 p-5" x-show="step>=1" x-transition.opacity x-cloak>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-sm font-semibold text-slate-800">Uploaded Photo</h3>
              <span class="text-xs text-slate-500" x-show="!photoUrl">No photo</span>
            </div>
            <button type="button" class="w-full block group focus:outline-none" :disabled="!photoUrl" @click="openLightbox(photoUrl)" :class="{'cursor-zoom-in': !!photoUrl, 'cursor-not-allowed opacity-60': !photoUrl}">
              <div class="aspect-square w-full overflow-hidden rounded-xl border-2 border-slate-300 bg-slate-50 flex items-center justify-center">
                <template x-if="photoUrl">
                  <img :src="photoUrl" alt="Uploaded Photo Preview" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-150">
                </template>
                <template x-if="!photoUrl">
                  <div class="text-slate-400 text-sm py-12">No photo selected</div>
                </template>
              </div>
              <p class="mt-2 text-xs text-emerald-700" x-show="photoUrl">Click to view full screen</p>
            </button>
          </div>

          <div class="bg-white rounded-2xl shadow-md ring-1 ring-emerald-900/10 p-5">
            <h3 class="text-sm font-semibold text-slate-800">Submission Tips</h3>
            <ul class="mt-3 space-y-2 text-sm text-slate-700 list-disc list-inside pl-1">
              <li>Use your official student number.</li>
              <li>Names must match school records.</li>
              <li>Photo needs a white background with even lighting.</li>
            </ul>
            <div class="mt-5 text-xs text-slate-500">Questions? Visit the Registrar office.</div>
          </div>

          <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/60 rounded-2xl ring-1 ring-emerald-900/10 p-5">
            <h3 class="text-sm font-semibold text-emerald-900">Processing Time</h3>
            <p class="mt-2 text-xs text-emerald-900/80">Submissions are reviewed within 3&ndash;5 working days. You'll be notified when your ID is ready.</p>
          </div>
        </div>
      </aside>

      <!-- Fullscreen Lightbox -->
      <div x-cloak x-show="lightboxOpen" x-transition.opacity @keydown.escape.window="closeLightbox" @click.self="closeLightbox" class="fixed inset-0 z-[100] bg-black/95 flex items-center justify-center">
        <button type="button" class="absolute top-4 right-4 bg-white/90 hover:bg-white text-slate-800 rounded-full shadow px-4 py-1.5 text-sm font-semibold" @click="closeLightbox">Close</button>
        <img :src="lightboxSrc" alt="Photo" class="max-w-[96vw] max-h-[96vh] w-auto h-auto object-contain rounded-lg">
      </div>
    </div>
  </main>

  <!-- Tailwind component aliases (kept minimal; most styles are in app.css) -->
  <style>
    .form-label{ @apply block text-sm font-medium text-slate-800; }
    .form-input-solid{ @apply mt-1 w-full rounded-xl border-2 border-slate-300 bg-white px-3 py-2 shadow-sm focus:outline-none focus:border-emerald-600 focus:ring-0; }
    .form-file-solid{ @apply mt-1 block w-full rounded-xl border-2 border-slate-300 bg-white px-3 py-2 shadow-sm focus:outline-none focus:border-emerald-600; }
    .form-file-solid::file-selector-button{ @apply mr-3 px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 cursor-pointer; }
    .form-error{ @apply mt-1 text-xs text-rose-600; }
    .btn-primary-solid{ @apply inline-flex items-center justify-center rounded-xl bg-emerald-600 px-6 py-2.5 text-white font-semibold shadow hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500; }
    .btn-ghost-solid{ @apply inline-flex items-center justify-center rounded-xl bg-white px-6 py-2.5 text-slate-800 font-semibold shadow ring-1 ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500; }
    .radio-pill input{ @apply peer sr-only; }
    .radio-pill span{ @apply inline-flex items-center rounded-full px-3 py-1.5 border-2 border-slate-300 text-slate-700 font-medium cursor-pointer select-none peer-checked:border-emerald-600 peer-checked:text-emerald-700; }
    .summary-row dt{ @apply px-4 py-3 text-[13px] font-medium text-slate-600; }
    .summary-row dd{ @apply px-4 py-3 text-sm text-slate-900; }
  </style>

  <!-- Alpine controller -->
  <script>
    function wizard(){
      return {
        step: 0,
        confirmed: false,
        submitting: false,
        photoUrl: null,
        imageError: '',
        faceStatus: '',
        faceError: '',
        faceEmbedding: null,
        faceBusy: false,
        faceConfidence: null,
        loading: false,
        lightboxOpen: false,
        lightboxSrc: null,
        _navTimer: null,

        sameAddress: false,

        form: {
          id_number:        @json(session('portal_user.student_number')),
          last_name:        @json(old('last_name', '')),
          first_name:       @json(old('first_name', '')),
          middle_initial:   @json(old('middle_initial', '')),
          gender:           @json(old('gender', '')),
          course:           @json(old('course', '')),
          blood_type:       @json(old('blood_type', '')),
          address:          @json(old('address', '')),
          guardian_name:    @json(old('guardian_name', '')),
          parent_address:   @json(old('parent_address', '')),
          guardian_contact: @json(old('guardian_contact', '')),
        },

        get progress(){ return ((this.step + 1) / 3) * 100; },
        get photoOk(){ return !!this.photoUrl && !this.imageError && !!this.faceEmbedding && !this.faceBusy; },

        initFromServer(){
          this.$watch('form.address', (value) => {
            if(this.sameAddress){
              this.form.parent_address = value || '';
            }
          });

          this.$watch('sameAddress', (value) => {
            if(value){
              this.form.parent_address = this.form.address || '';
            }
          });

          @if ($errors->has('picture_path')) this.step = 1; @endif
          @if ($errors->has('confirm')) this.step = 2; @endif

          if((this.form.address || '') && this.form.address === this.form.parent_address){
            this.sameAddress = true;
          }
        },

        goTo(n){
          if(n < 0 || n > 2) return;
          if (this._navTimer) { clearTimeout(this._navTimer); this._navTimer = null; }
          this.loading = true;
          this._navTimer = setTimeout(() => {
            this.step = n;
            this.loading = false;
            this._navTimer = null;
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }, 450);
        },

        openLightbox(src){ if(!src) return; this.lightboxSrc = src; this.lightboxOpen = true; document.documentElement.style.overflow='hidden'; },
        closeLightbox(){ this.lightboxOpen=false; this.lightboxSrc=null; document.documentElement.style.overflow=''; },

        digitsMask(e,max){ e.target.value = e.target.value.replace(/\D+/g,'').slice(0,max||11); this.form.guardian_contact = e.target.value; },
        oneLetter(e){ e.target.value = e.target.value.replace(/[^A-Za-z]/g,'').toUpperCase().slice(0,1); this.form.middle_initial = e.target.value; },
        capitalizeWords(value = '', preserveTrailingSpace = false){
          const raw = (value ?? '').toString();
          const keepTrailing = preserveTrailingSpace && /\S/.test(raw) && /\s$/.test(raw);
          const normalized = raw
            .replace(/\s+/g, ' ')
            .trimStart()
            .split(' ')
            .map(word => word ? word.charAt(0).toUpperCase() + word.slice(1).toLowerCase() : '')
            .filter(Boolean)
            .join(' ');
          if(!normalized){
            return '';
          }
          return keepTrailing ? `${normalized} ` : normalized;
        },
        setCapitalized(field, rawValue){
          const preserveTrailingSpace = /\s$/.test(rawValue || '');
          this.form[field] = this.capitalizeWords(rawValue || '', preserveTrailingSpace);
        },

        previewPhoto(ev){
          this.imageError = '';
          this.photoUrl = null;
          this.faceStatus = '';
          this.faceError = '';
          this.faceEmbedding = null;
          this.faceConfidence = null;
          if(this.$refs?.embedding){ this.$refs.embedding.value = ''; }
          const f = ev.target.files?.[0];
          if(!f){
            this.imageError = 'Please select an image.';
            return;
          }
          const okType = ['image/jpeg','image/png'].includes(f.type);
          const okSize = f.size <= 5*1024*1024;
          if(!okType){
            this.imageError = 'Only JPG/PNG allowed.';
            return;
          }
          if(!okSize){
            this.imageError = 'Max size is 5MB.';
            return;
          }
          const previousUrl = this.photoUrl;
          if(previousUrl){ URL.revokeObjectURL(previousUrl); }
          const url = URL.createObjectURL(f); const img = new Image();
          img.onload = async () => {
            const square = Math.abs(img.width - img.height) <= 2;
            if(!square || img.width < 300 || img.height < 300){
              this.imageError = 'Photo must be square (1:1) and at least 300x300px.';
              URL.revokeObjectURL(url);
              return;
            }
            this.photoUrl = url;
            if(window.FaceVerifier){
              try{
                this.faceBusy = true;
                this.faceStatus = 'Loading face models?';
                await window.FaceVerifier.prepare();
                this.faceStatus = 'Analyzing photo?';
                const result = await window.FaceVerifier.embedFile(f, { minConfidence: 0.5 });
                this.faceEmbedding = result.vector;
                this.faceConfidence = result.overview?.confidence ?? null;
                if(this.$refs?.embedding){
                  this.$refs.embedding.value = JSON.stringify(this.faceEmbedding);
                }
                const pct = this.faceConfidence !== null ? ` (${(this.faceConfidence*100).toFixed(1)}% confidence)` : '';
                this.faceStatus = 'Face captured successfully' + pct;
                this.faceError = '';
              }catch(err){
                this.faceEmbedding = null;
                this.faceConfidence = null;
                this.faceStatus = '';
                if(this.$refs?.embedding){
                  this.$refs.embedding.value = '';
                }
                const code = err?.message || '';
                if(code === 'face-not-found'){
                  this.faceError = 'No face detected. Retake with the subject facing the camera.';
                }else if(code === 'descriptor-empty'){
                  this.faceError = 'Unable to read facial features. Try a clearer image.';
                }else{
                  this.faceError = 'Face verification failed. Check lighting and try again.';
                  console.error(err);
                }
                this.photoUrl = null;
                URL.revokeObjectURL(url);
              }finally{
                this.faceBusy = false;
              }
            }else{
              this.faceStatus = 'Face verifier script is unavailable.';
              this.faceEmbedding = null;
              if(this.$refs?.embedding){
                this.$refs.embedding.value = '';
              }
            }
          };
          img.onerror = () => { this.imageError = 'Invalid image file.'; URL.revokeObjectURL(url); };
          img.src = url;
        },

        onSubmit(e){
          const chk = document.getElementById('finalConfirm');
          this.confirmed = !!(chk && chk.checked);
          if(!this.confirmed){
            e.preventDefault();
            this.step = 2;
            return;
          }
          if(!this.faceEmbedding){
            e.preventDefault();
            this.step = 1;
            this.faceError = 'Run face verification before Submitting…';
            return;
          }
          this.submitting = true;
        }
      }
    }
  </script>
</body>
</html>









































