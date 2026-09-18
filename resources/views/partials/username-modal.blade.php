@php
    $user = auth()->user();
@endphp

@if ($user && !in_array($user->role, ['admin', 'kesiswaan'], true) && !$user->username)
    <div id="username-modal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         role="dialog" aria-modal="true" aria-labelledby="username-modal-title">
        <div class="relative w-full max-w-md pt-48">
            <img src="{{ asset('images/model.png') }}" alt="Ilustrasi SOUL"
                 class="absolute top-0 left-1/2 -translate-x-1/2 w-48 max-w-[12rem] h-auto object-contain drop-shadow-xl">
            <div class="relative bg-white rounded-3xl shadow-2xl overflow-hidden">
                <div class="p-8 text-center">
                <h2 id="username-modal-title" class="text-xl font-extrabold text-slate-900">Lengkapi Username</h2>
                <p class="text-sm text-slate-500 mt-1 mb-5">Selamat datang! Pilih username unik supaya mudah dipakai untuk login berikutnya.</p>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold text-left">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="username-form" action="{{ route('username.store') }}" method="POST" novalidate>
                    @csrf

                    <label for="username-input" class="sr-only">Username</label>
                    <input id="username-input" type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username"
                           required minlength="3" maxlength="255" autofocus
                           pattern="[A-Za-z0-9._]+" inputmode="text" autocomplete="off"
                           aria-describedby="username-hint username-live-error"
                           class="w-full h-12 px-4 rounded-2xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:bg-white focus:border-blue-500 transition">

                    <p id="username-live-error" class="text-xs text-red-500 mt-1.5 text-left hidden"></p>

                    <button type="submit" id="username-submit-btn"
                            class="w-full h-12 mt-3 rounded-2xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-bold shadow-lg shadow-blue-500/30 transition">
                        Simpan Username
                    </button>
                </form>

                <p id="username-hint" class="text-[11px] text-slate-400 mt-3">Username hanya boleh huruf, angka, titik (.) dan underscore (_).</p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modal = document.getElementById('username-modal');
            var form = document.getElementById('username-form');
            var input = document.getElementById('username-input');
            var submitBtn = document.getElementById('username-submit-btn');
            var liveError = document.getElementById('username-live-error');
            var validPattern = /^[A-Za-z0-9._]*$/;

            // Kunci scroll di belakang modal selama modal tampil.
            if (modal) {
                document.documentElement.style.overflow = 'hidden';
            }

            function validate() {
                var value = input.value;
                if (value.length === 0) {
                    liveError.classList.add('hidden');
                    return true;
                }
                if (!validPattern.test(value)) {
                    liveError.textContent = 'Hanya huruf, angka, titik (.) dan underscore (_) yang diperbolehkan.';
                    liveError.classList.remove('hidden');
                    return false;
                }
                if (value.length < 3) {
                    liveError.textContent = 'Username minimal 3 karakter.';
                    liveError.classList.remove('hidden');
                    return false;
                }
                liveError.classList.add('hidden');
                return true;
            }

            if (input) {
                input.addEventListener('input', validate);
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!validate()) {
                        e.preventDefault();
                        return;
                    }
                    // Cegah double submit.
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Menyimpan...';
                });
            }
        })();
    </script>
@endif