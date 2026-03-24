<!doctype html>
<html class="h-full">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar — FinReady</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: "class" };</script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
      .role-tab { transition: all .18s; }
      .role-tab.active {
        background: #16a34a;
        color: #fff;
        box-shadow: 0 2px 8px rgba(22,163,74,.3);
      }
      .role-tab:not(.active) { color: #6b7280; background: transparent; }
      .role-tab:not(.active):hover { background: #f3f4f6; color: #111827; }

      /* Eye button smooth swap */
      .eye-btn { transition: color .15s; }
      .eye-btn svg.hidden { display: none !important; }
      .eye-btn svg:not(.hidden) { display: block; }
    </style>
  </head>

  <body class="h-full bg-gray-100 dark:bg-gray-900 transition-colors">

    <div class="absolute top-5 left-5 z-10">
      <a href="index.html"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-green-400 hover:text-green-600 transition">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
      </a>
    </div>

    <div class="absolute top-5 right-5 z-10">
      <button onclick="toggleDark()"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-400 transition">
        <svg id="iconDark" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <svg id="iconLight" class="hidden" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        Mode
      </button>
    </div>

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">

      <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
        <div class="flex justify-center mb-5">
          <a href="index.html"
            class="bg-green-500 hover:bg-green-600 text-white w-12 h-12 rounded-xl flex items-center justify-center font-bold transition shadow-md shadow-green-200 dark:shadow-none">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
              <path d="M3 3v18h18"/><path d="m7 16 4-4 4 4 4-8"/>
            </svg>
          </a>
        </div>
        <h2 id="registerTitle" class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
          Daftar UMKM
        </h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Sudah punya akun?
          <a id="loginLink" href="{{ route('authenticate.login') }}"
            class="font-semibold text-green-600 hover:text-green-500 transition">
            Masuk di sini
          </a>
        </p>
      </div>

      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm">

        <div class="flex gap-1.5 p-1.5 bg-gray-200 dark:bg-gray-700 rounded-xl mb-7">
          <button type="button" id="tabUMKM" onclick="switchRole('umkm')"
            class="role-tab active flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            UMKM
          </button>
          <button type="button" id="tabInvestor" onclick="switchRole('investor')"
            class="role-tab flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
            Investor
          </button>
        </div>

        <form class="space-y-4" method="POST" action="{{ route('authenticate.register.post') }}" onsubmit="showLoading()">
          @csrf
          <input type="hidden" name="role" id="roleInput" value="umkm">

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
              Nama Lengkap
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <input type="text" name="name" required placeholder="Nama Lengkap" value="{{ old('name') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-3 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-sm" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
              Email
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
              </span>
              <input type="email" name="email" required placeholder="nama@email.com" value="{{ old('email') }}"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-3 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-sm" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input id="pwInput" type="password" name="password" required placeholder="••••••••"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-10 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-sm" />
              <button type="button" onclick="togglePw('pwInput', this)"
                class="eye-btn absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="eye-closed hidden" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
              </span>
              <input id="pwConfirmInput" type="password" name="password_confirmation" required placeholder="••••••••"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-10 pr-10 py-2.5 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-sm" />
              <button type="button" onclick="togglePw('pwConfirmInput', this)"
                class="eye-btn absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="eye-open" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="eye-closed hidden" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-start gap-2 pt-1">
            <input id="terms" type="checkbox" name="terms" required
              class="mt-0.5 w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer accent-green-600" />
            <label for="terms" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
              Saya setuju dengan <a href="#" class="text-green-600 hover:text-green-500 font-medium">Syarat & Ketentuan</a>
            </label>
          </div>

          <button type="submit" id="submitBtn"
            class="w-full bg-green-600 hover:bg-green-700 active:scale-[.98] text-white font-semibold py-2.5 rounded-lg transition-all flex items-center justify-center gap-2 text-sm shadow-sm shadow-green-300 dark:shadow-none mt-2">
            <span id="btnText">Daftar</span>
            <svg id="btnSpinner" class="hidden animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/>
              <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
          </button>

        </form>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
      const params = new URLSearchParams(location.search);
      if (params.get('role') === 'investor') switchRole('investor');

      function switchRole(role) {
        const isUMKM = role === 'umkm';
        document.getElementById('roleInput').value = role; 
        
        document.getElementById('tabUMKM').classList.toggle('active', isUMKM);
        document.getElementById('tabInvestor').classList.toggle('active', !isUMKM);
        document.getElementById('registerTitle').textContent = isUMKM ? 'Daftar UMKM' : 'Daftar Investor';
      }

      // Fungsi togglePw dimodifikasi agar dinamis menerima scope elemen button
      function togglePw(inputId, btnElement) {
        const inp = document.getElementById(inputId);
        const isHidden = inp.type === 'password';
        inp.type = isHidden ? 'text' : 'password';
        
        btnElement.querySelector('.eye-open').classList.toggle('hidden', isHidden);
        btnElement.querySelector('.eye-closed').classList.toggle('hidden', !isHidden);
      }

      function showLoading() {
        const btn = document.getElementById('submitBtn');
        const txt = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');
        
        txt.textContent = 'Memproses...';
        spinner.classList.remove('hidden');
        btn.classList.add('opacity-80', 'cursor-not-allowed');
      }

      function toggleDark() {
        const isDark = document.documentElement.classList.toggle('dark');
        document.getElementById('iconDark').classList.toggle('hidden', isDark);
        document.getElementById('iconLight').classList.toggle('hidden', !isDark);
      }

      // Konfigurasi Toastr
      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "4000",
      };

      // Menangkap Error dari Laravel dan menampilkannya di Toastr
      @if ($errors->any())
        @foreach ($errors->all() as $error)
          toastr.error("{{ $error }}");
        @endforeach
      @endif

      // Menangkap session
      @if (session('success'))
        toastr.success("{{ session('success') }}");
      @endif
      
      @if (session('error'))
        toastr.error("{{ session('error') }}");
      @endif
    </script>
  </body>
</html>