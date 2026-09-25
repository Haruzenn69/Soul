@auth
    @if(auth()->user()->needsOnboarding() && auth()->user()->needsProfileCompletion())
        @php
            $profileUrl = auth()->user()->role === 'pembina'
                ? route('pembina.profile')
                : route('siswa.profile.edit');
        @endphp
        <style>
            @keyframes onboardingFadeUp { from { opacity: 0; transform: translateY(18px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
            @keyframes onboardingPulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, .55); } 50% { box-shadow: 0 0 0 12px rgba(56, 189, 248, 0); } }
            @keyframes onboardingBob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
            .onboarding-anim { animation: onboardingFadeUp .4s cubic-bezier(.22, 1, .36, 1) both; }
            .onboarding-badge-anim { animation: onboardingBob 1.4s ease-in-out infinite; }
        </style>

        <div id="onboarding-overlay" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

            <div id="onboarding-badge" class="absolute z-10 hidden onboarding-badge-anim pointer-events-none"></div>

            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden onboarding-anim">
                <div class="px-6 pt-6 pb-5 bg-gradient-to-br from-sky-400 to-blue-600 text-white">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 border border-white/30 backdrop-blur flex items-center justify-center text-xl mb-3">📝</div>
                    <h2 class="text-base font-extrabold">Ayo lengkapi profile Anda</h2>
                    <p class="text-[11px] text-white/80 mt-1 leading-relaxed">Data diri kamu masih kosong. Isi nama lengkap, kelas, dan jenis kelamin agar bisa mendaftar ekskul.</p>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ $profileUrl }}"
                           class="flex-1 text-center px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-200 transition hover:-translate-y-0.5">
                            Ke Halaman Profile ➜
                        </a>
                        <form method="POST" action="{{ route('onboarding.complete') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-500 text-xs font-bold rounded-xl transition">
                                Nanti Saja
                            </button>
                        </form>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-3 text-center">Fitur daftar ekskul baru aktif setelah profil lengkap.</p>
                </div>
            </div>
        </div>

        <script>
            (function () {
                var overlay = document.getElementById('onboarding-overlay');
                var badge = document.getElementById('onboarding-badge');
                var target = document.querySelector('[data-onboarding-profile]');

                if (target) {
                    function positionSpotlight() {
                        var r = target.getBoundingClientRect();
                        spot.style.left = (r.left - 5) + 'px';
                        spot.style.top = (r.top - 5) + 'px';
                        spot.style.width = (r.width + 10) + 'px';
                        spot.style.height = (r.height + 10) + 'px';
                        badge.style.left = (r.right + 14) + 'px';
                        badge.style.top = (r.top + r.height / 2 - 16) + 'px';
                    }
                    var spot = document.createElement('div');
                    spot.id = 'onboarding-spot';
                    spot.className = 'hidden';
                    spot.style.cssText = 'position:fixed;z-index:11;border-radius:14px;border:2px solid rgba(56,189,248,.95);box-shadow:0 0 0 4px rgba(56,189,248,.25), 0 0 28px rgba(56,189,248,.65);animation:onboardingPulse 1.6s ease-out infinite;background:rgba(255,255,255,.08);pointer-events:none;';
                    overlay.appendChild(spot);
                    spot.classList.remove('hidden');

                    var label = target.querySelector('.sidebar-label').textContent.trim() || 'Profile';
                    badge.innerHTML = '<div class="flex items-center gap-2 bg-white text-sky-700 text-xs font-extrabold pl-3 pr-4 py-2 rounded-2xl shadow-xl border border-sky-100 whitespace-nowrap">Klik di sini — menu <span class="text-sky-400">' + label + '</span> ←</div>';
                    badge.classList.remove('hidden');

                    positionSpotlight();
                    window.addEventListener('resize', positionSpotlight);
                    window.addEventListener('load', positionSpotlight);
                }

                overlay.classList.remove('hidden');
            })();
        </script>
    @endif
@endauth