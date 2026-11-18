@auth
    @if(auth()->user()->needsOnboarding() && !session('onboarding_dismissed'))
        @php
            $authUser = auth()->user();
            $roleLabel = $authUser->role === 'pembina' ? 'Guru / Pembina' : 'Siswa';
            $currentModel = $authUser->siswa ?? $authUser->pembina;
            $namaLengkap = $currentModel?->nama ?? $authUser->username ?? 'Pengguna';
            $profileEditUrl = $authUser->role === 'pembina' ? route('pembina.profile') : route('siswa.profile.edit');
        @endphp

        <div class="fixed inset-0 z-[120] flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="onboarding-title">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md"></div>

            <section class="relative z-10 my-8 w-full max-w-md overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-2xl animate-fade-up">
                <header class="bg-gradient-to-br from-sky-500 via-sky-600 to-blue-600 px-6 py-6 text-white text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <p class="mt-4 text-[11px] font-bold uppercase tracking-wide text-sky-100">Login pertama kali · {{ $roleLabel }}</p>
                    <h2 id="onboarding-title" class="mt-2 text-lg font-extrabold leading-tight">
                        Ayo edit profilmu terlebih dahulu!
                    </h2>
                    <p class="mt-2 text-xs leading-relaxed text-sky-100">
                        Lengkapi data akunmu (email, nomor telepon, jenis kelamin, tanggal lahir, tempat lahir, dan agama) sebelum mulai menggunakan aplikasi.
                    </p>
                </header>

                <div class="p-6 space-y-3">
                    <a href="{{ $profileEditUrl }}"
                       class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-lg shadow-sky-200 transition hover:-translate-y-0.5 hover:from-sky-600 hover:to-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 16l-4 1 1-4 8.5-8.5z"/>
                        </svg>
                        Ke Profile
                    </a>

                    <form method="POST" action="{{ route('onboarding.dismiss') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">
                            Nanti Saja
                        </button>
                    </form>
                </div>
            </section>
        </div>
    @endif
@endauth
