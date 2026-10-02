@auth
    @if(auth()->user()->needsOnboarding())
        @php
            $authUser = auth()->user();
            $roleLabel = $authUser->role === 'pembina' ? 'Guru / Pembina' : 'Siswa';
            $currentModel = $authUser->siswa ?? $authUser->pembina;
            $namaLengkap = $currentModel?->nama ?? $authUser->username;
        @endphp

        <div class="fixed inset-0 z-[120] flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="onboarding-title">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md"></div>

            <section class="relative z-10 my-8 w-full max-w-md overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-2xl">
                <header class="bg-gradient-to-br from-sky-500 via-sky-600 to-blue-600 px-6 py-5 text-white">
                    <p class="text-[11px] font-bold uppercase tracking-wide text-sky-100">Login pertama kali · {{ $roleLabel }}</p>
                    <h2 id="onboarding-title" class="mt-2 text-lg font-extrabold leading-tight">
                        Selamat datang, {{ $namaLengkap }}!
                    </h2>
                    <p class="mt-1 text-xs leading-relaxed text-sky-100">
                        Buat username dan password baru untuk mengamankan akun Anda.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="mx-6 mt-5 rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-xs font-semibold text-rose-700" role="alert">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="onboarding-form" method="POST" action="{{ route('onboarding.setup') }}" class="space-y-4 p-6" novalidate>
                    @csrf

                    <div>
                        <label for="onboarding-username" class="mb-1.5 block text-xs font-bold text-slate-600">Username</label>
                        <input type="text" name="username" id="onboarding-username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9_-]+" autofocus autocomplete="username"
                               value="{{ old('username', $authUser->username ?? '') }}" placeholder="Masukkan username"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-800 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <p id="onboarding-username-alert" class="mt-2 hidden items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700" role="alert" aria-live="polite">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-1.5 0V7Zm.75 7.25a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75Z" clip-rule="evenodd"/></svg>
                            <span>Username kurang dari 3 karakter.</span>
                        </p>
                        <p class="mt-1 text-[11px] text-slate-400">Minimal 3 karakter; gunakan huruf, angka, tanda hubung, atau garis bawah.</p>
                    </div>

                    <div>
                        <label for="onboarding-password" class="mb-1.5 block text-xs font-bold text-slate-600">Password baru</label>
                        <input type="password" name="password" id="onboarding-password" required minlength="8" autocomplete="new-password" placeholder="Minimal 8 karakter"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <p id="onboarding-password-alert" class="mt-2 hidden items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700" role="alert" aria-live="polite">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-1.5 0V7Zm.75 7.25a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75Z" clip-rule="evenodd"/></svg>
                            <span>Password baru harus minimal 8 karakter.</span>
                        </p>
                    </div>

                    <div>
                        <label for="onboarding-password-confirmation" class="mb-1.5 block text-xs font-bold text-slate-600">Konfirmasi password baru</label>
                        <input type="password" name="password_confirmation" id="onboarding-password-confirmation" required minlength="8" autocomplete="new-password" placeholder="Ulangi password baru"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <p id="onboarding-password-confirmation-alert" class="mt-2 hidden items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700" role="alert" aria-live="polite">
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm-.75-11a.75.75 0 0 1 1.5 0v3.5a.75.75 0 0 1-1.5 0V7Zm.75 7.25a.875.875 0 1 0 0-1.75.875.875 0 0 0 0 1.75Z" clip-rule="evenodd"/></svg>
                            <span>Konfirmasi password harus sama dengan password baru.</span>
                        </p>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-200 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
                        Simpan dan lanjutkan
                    </button>
                </form>

                <form method="POST" action="{{ route('onboarding.login') }}" class="px-6 pb-6">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">
                        Kembali ke login
                    </button>
                </form>
            </section>
        </div>

        <script>
            (() => {
                const usernameInput = document.getElementById('onboarding-username');
                const usernameAlert = document.getElementById('onboarding-username-alert');
                const form = document.getElementById('onboarding-form');
                const passwordInput = document.getElementById('onboarding-password');
                const passwordAlert = document.getElementById('onboarding-password-alert');
                const confirmationInput = document.getElementById('onboarding-password-confirmation');
                const confirmationAlert = document.getElementById('onboarding-password-confirmation-alert');

                if (!usernameInput || !usernameAlert || !form || !passwordInput || !passwordAlert || !confirmationInput || !confirmationAlert) return;

                const updateUsernameAlert = () => {
                    const isTooShort = usernameInput.value.length > 0 && usernameInput.value.length < 3;
                    usernameAlert.classList.toggle('hidden', !isTooShort);
                    usernameAlert.classList.toggle('flex', isTooShort);
                    usernameInput.setAttribute('aria-invalid', isTooShort ? 'true' : 'false');
                };

                const updatePasswordAlerts = () => {
                    const passwordTooShort = passwordInput.value.length > 0 && passwordInput.value.length < 8;
                    const confirmationMismatch = confirmationInput.value.length > 0 && confirmationInput.value !== passwordInput.value;

                    passwordAlert.classList.toggle('hidden', !passwordTooShort);
                    passwordAlert.classList.toggle('flex', passwordTooShort);
                    confirmationAlert.classList.toggle('hidden', !confirmationMismatch);
                    confirmationAlert.classList.toggle('flex', confirmationMismatch);
                    passwordInput.setAttribute('aria-invalid', passwordTooShort ? 'true' : 'false');
                    confirmationInput.setAttribute('aria-invalid', confirmationMismatch ? 'true' : 'false');
                };

                usernameInput.addEventListener('input', updateUsernameAlert);
                passwordInput.addEventListener('input', updatePasswordAlerts);
                confirmationInput.addEventListener('input', updatePasswordAlerts);
                form.addEventListener('submit', (event) => {
                    const username = usernameInput.value.trim();

                    if (username.length < 3) {
                        event.preventDefault();
                        window.alert('Username harus minimal 3 karakter.');
                        usernameInput.focus();
                        return;
                    }

                    if (passwordInput.value.length < 8) {
                        event.preventDefault();
                        window.alert('Password baru harus minimal 8 karakter.');
                        passwordInput.focus();
                        return;
                    }

                    if (!confirmationInput.value || confirmationInput.value !== passwordInput.value) {
                        event.preventDefault();
                        window.alert('Konfirmasi password harus sama dengan password baru.');
                        confirmationInput.focus();
                    }
                });

                updateUsernameAlert();
                updatePasswordAlerts();
            })();
        </script>
    @endif
@endauth
