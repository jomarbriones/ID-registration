<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account - Student ID Registration Portal</title>
  <link rel="icon" href="{{ asset('images/CvSU-navbar-Logo-PNG.png') }}">
  @vite(['resources/css/app.css'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' };</script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
@php
  $studentError = $errors->first('student_number');
  $passwordError = $errors->first('password');
@endphp
<body>
  <div class="auth-shell">
    <div class="auth-card lg:grid-cols-[1.1fr_minmax(0,1fr)]">
      <section class="auth-illustration">
        <div>
          <p class="text-sm uppercase tracking-[0.35em] text-white/70">CvSU Naic</p>
          <h2>Build your Student ID profile with confidence.</h2>
          <p class="mt-4 text-base text-white/80">
            Create your portal account in minutes. Verify your student number, add a secure password, and you're ready to log in.
          </p>
        </div>
        <div class="space-y-3 text-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-lg text-white">&check;</span>
            Verified Naic enrollees only.
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-lg text-white">&check;</span>
            Live password strength guidance.
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/20 text-lg text-white">&check;</span>
            Real-time student number lookup.
          </div>
        </div>
        <p class="text-sm text-white/70">
          Already registered? <a class="underline-offset-2 hover:underline" href="{{ route('login') }}">Return to login</a>
        </p>
      </section>

      <section
        x-data='registerForm({
          studentNumber: @js(old("student_number")),
          validationEndpoint: @js(route("auth.check-student")),
          studentError: @js($studentError),
          passwordError: @js($passwordError),
        })'
        class="space-y-6"
      >
        <div class="space-y-2">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 font-semibold grid place-items-center">New</div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-600">Quick setup</p>
              <h1 class="text-2xl font-semibold text-neutral-900">Create your CvSU Portal account</h1>
            </div>
          </div>
          <p class="text-sm text-neutral-500">Verify your student number and set a password to continue.</p>
        </div>

        @if ($errors->any())
          <div class="alert-danger">
            <div class="font-semibold">Registration error</div>
            <p class="text-sm">{{ $errors->first() }}</p>
          </div>
        @endif

        <template x-if="studentStatus">
          <div class="rounded-2xl border px-4 py-3 text-sm"
               x-transition.opacity
               role="status"
               aria-live="polite"
               :class="studentStatus.state === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : (studentStatus.state === 'loading' ? 'border-neutral-200 bg-neutral-50 text-neutral-500' : 'border-red-200 bg-red-50 text-red-700')}">
            <p class="font-semibold" x-text="studentStatus.title"></p>
            <p x-text="studentStatus.message"></p>
          </div>
        </template>

        <form
          method="POST"
          action="{{ url('/create-account') }}"
          class="space-y-6"
          x-ref="registerForm"
          @submit="handleSubmit($event)"
        >
          @csrf

          <section class="space-y-4" aria-label="Student information">
            <header>
              <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Student info</p>
              <h2 class="text-lg font-semibold text-neutral-900">Verify your student number</h2>
            </header>
            <div class="form-floating">
              <input id="student_number" name="student_number" inputmode="numeric" pattern="\d{9}" maxlength="9"
                     placeholder="123456789" x-model="studentNumber" autocomplete="off"
                     @input.debounce.400ms="autoValidateStudent($event.target.value)"
                     @blur="autoValidateStudent($event.target.value)"
                     :aria-invalid="(studentStatus?.state === 'error') || Boolean(serverStudentError)"
                     aria-describedby="student-number-hint">
              <label for="student_number">Student Number (9 digits)</label>
            </div>
            <p id="student-number-hint" class="form-hint">Must match the CvSU Naic enrollment records.</p>
            <template x-if="serverStudentError">
              <p class="form-error" x-text="serverStudentError"></p>
            </template>
          </section>

          <section class="space-y-4" aria-label="Account security">
            <header>
              <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Account info</p>
              <h2 class="text-lg font-semibold text-neutral-900">Create a strong password</h2>
            </header>
            <div class="form-floating">
              <input :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="Password" x-model="passwordValue"
                     autocomplete="new-password" aria-describedby="password-hint" @input="updateStrength" required>
              <label for="password">Password</label>
              <button type="button" class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-emerald-600"
                      @click="showPassword = !showPassword">
                <span x-text="showPassword ? 'Hide' : 'Show'"></span>
              </button>
            </div>
            <div class="form-floating">
              <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"
                     x-model="passwordConfirmValue" autocomplete="new-password" aria-describedby="password-hint" @input="updateStrength" required>
              <label for="password_confirmation">Confirm Password</label>
              <button type="button" class="absolute inset-y-0 right-3 flex items-center text-sm font-semibold text-emerald-600"
                      @click="showConfirm = !showConfirm">
                <span x-text="showConfirm ? 'Hide' : 'Show'"></span>
              </button>
            </div>
            <div>
              <p id="password-hint" class="text-sm font-semibold text-neutral-600">Password strength</p>
              <div class="mt-2 flex gap-1">
                <template x-for="index in 4" :key="index">
                  <span class="h-2 flex-1 rounded-full"
                        :class="strength >= index ? 'bg-emerald-500' : 'bg-[#E2E8F0]'" ></span>
                </template>
              </div>
              <p class="form-hint">Use at least 6 characters with a mix of numbers or symbols.</p>
            </div>
            <ul class="password-checklist" aria-live="polite">
              <li :data-valid="passwordHasLength">
                Meets minimum length (6 characters)
              </li>
              <li :data-valid="passwordHasNumber">
                Contains at least one number
              </li>
              <li :data-valid="passwordMatches">
                Matches confirmation
              </li>
            </ul>
            @if($passwordError)
              <p class="form-error">{{ $passwordError }}</p>
            @endif
            <label class="flex items-start gap-3 text-sm font-medium text-neutral-700">
              <input type="checkbox" x-model="confirmDetails" class="mt-1 h-4 w-4 rounded border-neutral-300 text-emerald-600 focus:ring-emerald-600">
              <span>I confirm the information above is accurate.</span>
            </label>
          </section>

          <button type="submit" class="btn-primary w-full" :disabled="submitDisabled">
            <span class="flex items-center justify-center gap-2">
              <span x-cloak x-show="submitLoading" class="btn-spinner"></span>Register
              <span x-text="submitLoading ? 'Submitting…' : 'Register'"></span>
            </span>
          </button>
        </form>

        @if(session('success'))
          <div x-data="{ open: true }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" aria-modal="true" role="dialog">
            <div x-show="open" x-transition.scale class="w-full max-w-md rounded-3xl bg-white p-6 text-center shadow-2xl ring-1 ring-emerald-100">
              <h3 class="text-2xl font-semibold text-emerald-700">Account created successfully!</h3>
              <p class="mt-2 text-sm text-neutral-600">Great job—now log in with your new credentials to start the ID registration process.</p>
              <div class="mt-6 space-y-3">
                <a href="{{ route('login') }}" class="btn-primary w-full">Go to login</a>
                <button class="btn-ghost w-full" @click="open = false">Stay here</button>
              </div>
            </div>
          </div>
        @endif
      </section>
    </div>
  </div>

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('registerForm', (config = {}) => ({
        studentNumber: config.studentNumber || '',
        studentStatus: null,
        serverStudentError: config.studentError || null,
        validationEndpoint: config.validationEndpoint,
        passwordValue: '',
        passwordConfirmValue: '',
        confirmDetails: false,
        strength: 0,
        showPassword: false,
        showConfirm: false,
        submitLoading: false,

        get passwordHasLength() {
          return this.passwordValue.length >= 6;
        },
        get passwordHasNumber() {
          return /[0-9]/.test(this.passwordValue);
        },
        get passwordMatches() {
          return this.passwordValue.length > 0 && this.passwordValue === this.passwordConfirmValue;
        },
        get canSubmit() {
          return this.studentStatus?.state === 'success' && this.passwordHasLength && this.passwordHasNumber && this.passwordMatches && this.confirmDetails;
        },
        get submitDisabled() {
          return this.submitLoading || !this.canSubmit;
        },

        updateStrength() {
          let score = 0;
          if (this.passwordValue.length >= 6) score++;
          if (/[A-Z]/.test(this.passwordValue)) score++;
          if (/[0-9]/.test(this.passwordValue)) score++;
          if (/[^A-Za-z0-9]/.test(this.passwordValue)) score++;
          this.strength = score;
        },

        autoValidateStudent(rawValue = null) {
          this.serverStudentError = null;
          const digits = (rawValue ?? this.studentNumber ?? '').replace(/\D/g, '').slice(0, 9);
          if (this.studentNumber !== digits) {
            this.studentNumber = digits;
          }
          if (digits.length !== 9) {
            this.studentStatus = null;
            return;
          }
          this.validateStudent(digits);
        },

        async validateStudent(digits = null) {
          const normalized = digits ?? (this.studentNumber || '').replace(/\D/g, '').slice(0, 9);
          if (!this.validationEndpoint || normalized.length !== 9) {
            this.studentStatus = {
              state: 'error',
              title: 'Incomplete student number',
              message: 'Please enter all 9 digits of your student number.',
            };
            return;
          }
          this.studentStatus = { state: 'loading', title: 'Checking registry...', message: 'Please wait while we confirm your record.' };
          try {
            const params = new URLSearchParams({ student_number: normalized });
            const response = await fetch(`${this.validationEndpoint}?${params.toString()}`, { headers: { Accept: 'application/json' } });
            const payload = await response.json();
            if (!response.ok || payload.status !== 'ok') {
              this.studentStatus = {
                state: 'error',
                title: 'Not found',
                message: payload.message || 'Student number not found or already registered.',
              };
              return;
            }
            this.studentStatus = {
              state: 'success',
              title: 'Student number found',
              message: payload.message || 'Great! You can continue creating your portal account.',
            };
          } catch (error) {
            this.studentStatus = { state: 'error', title: 'Network error', message: 'Unable to validate right now. Please try again.' };
          }
        },

        handleSubmit(event) {
          if (!this.canSubmit) {
            event.preventDefault();
            this.studentStatus = this.studentStatus || {
              state: 'error',
              title: 'Incomplete requirements',
              message: 'Please verify your student number and complete the password checklist.',
            };
            return;
          }
          this.submitLoading = true;
        },
      }));
    });
  </script>
</body>
</html>
