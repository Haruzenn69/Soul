<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ekskul->nama_ekskul }} - SOUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            blue: '#2563EB',
                            darkBlue: '#1D4ED8',
                            yellow: '#FACC15',
                            dark: '#0F172A',
                            light: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-theme-dark font-sans antialiased selection:bg-theme-yellow selection:text-theme-dark">

    <!-- NAVBAR -->
    <header class="px-6 md:px-8 py-4 flex items-center justify-between border-b border-gray-100 sticky top-0 bg-white/80 backdrop-blur-md z-50">
        <a href="/" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-theme-blue flex items-center justify-center text-white font-bold text-sm">S</div>
            <span class="font-extrabold tracking-tight text-lg text-theme-dark uppercase">SOUL</span>
        </a>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-theme-dark text-xs font-semibold rounded-full transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-theme-dark hover:bg-gray-800 text-white text-xs font-semibold rounded-full transition">Masuk &rarr;</a>
            @endauth
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-xs font-medium text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. HERO -->
    <section class="relative overflow-hidden bg-theme-dark text-white">
        @if($ekskul->cover)
            <img src="{{ asset('storage/' . $ekskul->cover) }}" alt="{{ $ekskul->nama_ekskul }}" class="absolute inset-0 w-full h-full object-cover opacity-40">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-theme-blue to-theme-darkBlue"></div>
        @endif
        <div class="relative z-10 max-w-5xl mx-auto px-6 py-24 text-center">
            <div class="flex justify-center mb-6">
                @if($ekskul->logo)
                    <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-20 h-20 object-contain rounded-2xl bg-white/90 p-2 shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-theme-yellow text-theme-dark flex items-center justify-center text-2xl font-extrabold shadow-lg">
                        {{ substr($ekskul->nama_ekskul, 0, 1) }}
                    </div>
                @endif
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight mb-3">{{ $ekskul->nama_ekskul }}</h1>
            @if($ekskul->tagline)
                <p class="text-theme-yellow font-semibold text-sm md:text-base mb-4">{{ $ekskul->tagline }}</p>
            @endif
            <p class="text-sm text-gray-200 max-w-xl mx-auto mb-8">{{ $ekskul->deskripsi }}</p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('siswa.form-daftar', $ekskul) }}" class="px-7 py-3 bg-theme-yellow hover:bg-yellow-400 text-theme-dark text-xs font-bold rounded-full transition shadow-lg {{ $ekskul->is_open_recruitment ? '' : 'opacity-50 pointer-events-none' }}">
                    {{ $ekskul->is_open_recruitment ? 'Gabung Ekskul' : 'Pendaftaran Ditutup' }}
                </a>
            </div>
        </div>
    </section>

    <!-- 2. QUICK INFO -->
    <section class="max-w-5xl mx-auto px-6 -mt-8 relative z-20">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0 divide-gray-100">
            <div class="p-5 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Jadwal</p>
                <p class="text-xs font-semibold">{{ $ekskul->jadwal ?? '-' }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Anggota</p>
                <p class="text-xs font-semibold">{{ $totalAnggota }} Anggota</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Pembina</p>
                <p class="text-xs font-semibold">{{ $ekskul->pembina->nama ?? '-' }}</p>
            </div>
            <div class="p-5 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Pelatih</p>
                <p class="text-xs font-semibold">{{ $ekskul->pelatih->nama ?? '-' }}</p>
            </div>
        </div>
    </section>

    <!-- 3. TENTANG -->
    @if($ekskul->tujuan || $ekskul->deskripsi)
    <section id="tentang" class="max-w-5xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">Tentang Kami</span>
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Kami berkenalan dulu, yuk!</h2>
                <p class="text-xs md:text-sm text-gray-600 leading-relaxed mb-4">{{ $ekskul->deskripsi }}</p>
                @if($ekskul->tujuan)
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed">{{ $ekskul->tujuan }}</p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-theme-light rounded-2xl p-6 text-center border border-gray-100">
                    <p class="text-2xl md:text-3xl font-extrabold text-theme-blue">{{ $totalAnggota }}+</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Anggota Aktif</p>
                </div>
                <div class="bg-theme-light rounded-2xl p-6 text-center border border-gray-100">
                    <p class="text-2xl md:text-3xl font-extrabold text-theme-yellow">{{ $ekskul->prestasis->count() }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Prestasi</p>
                </div>
                <div class="bg-theme-light rounded-2xl p-6 text-center border border-gray-100 col-span-2">
                    <p class="text-xs md:text-sm font-extrabold text-theme-dark">{{ $ekskul->kegiatans->count() }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">Kegiatan Terlaksana</p>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- 4. KEGIATAN -->
    @if($ekskul->kegiatans->isNotEmpty())
    <section id="kegiatan" class="bg-theme-light py-20">
        <div class="max-w-5xl mx-auto px-6">
            <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">Kegiatan Kami</span>
            <h2 class="text-2xl md:text-3xl font-bold mb-10">Apa yang Kami Lakukan?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ekskul->kegiatans as $kegiatan)
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition">
                        @if($kegiatan->dokumentasi)
                            <img src="{{ asset('storage/' . $kegiatan->dokumentasi) }}" alt="{{ $kegiatan->materi }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-theme-blue/10 flex items-center justify-center text-theme-blue">
                            <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                            </svg>
                        </div>
                        @endif
                        <div class="p-5">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">{{ $kegiatan->tanggal_kegiatan->translatedFormat('d F Y') }}</p>
                            <h3 class="font-bold text-sm mb-2">{{ $kegiatan->materi }}</h3>
                            @if($kegiatan->deskripsi)
                                <p class="text-xs text-gray-500 leading-relaxed">{{ \Illuminate\Support\Str::limit($kegiatan->deskripsi, 90) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 5. PRESTASI -->
    @if($ekskul->prestasis->isNotEmpty())
    <section id="prestasi" class="max-w-5xl mx-auto px-6 py-20">
        <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">Prestasi</span>
        <h2 class="text-2xl md:text-3xl font-bold mb-10 flex items-center gap-3">Kebanggaan Kami
                        <svg class="w-7 h-7 md:w-8 md:h-8 text-theme-yellow shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 4h10v4a5 5 0 01-10 0V4z"/>
                            <path d="M7 6H4v2a3 3 0 003 3M17 6h3v2a3 3 0 01-3 3m-5 4v5m-2.5 3h5a.5.5 0 00.5-.5v-2.5a.5.5 0 00-.5-.5h-5a.5.5 0 00-.5.5v2.5a.5.5 0 00.5.5z"/>
                        </svg>
                    </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($ekskul->prestasis as $prestasi)
                <div class="bg-theme-light rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-center text-theme-blue mb-3">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 4h10v4a5 5 0 01-10 0V4z"/>
                            <path d="M7 6H4v2a3 3 0 003 3M17 6h3v2a3 3 0 01-3 3m-5 4v5m-2.5 3h5a.5.5 0 00.5-.5v-2.5a.5.5 0 00-.5-.5h-5a.5.5 0 00-.5.5v2.5a.5.5 0 00.5.5z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base mb-1">{{ $prestasi->judul }}</h3>
                    @if($prestasi->kategori)
                        <p class="text-xs text-gray-500 mb-1">{{ $prestasi->kategori }}</p>
                    @endif
                    @if($prestasi->tahun)
                        <p class="text-[10px] text-theme-blue font-bold">{{ $prestasi->tahun }}</p>
                    @endif
                    @if($prestasi->foto)
                        <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="{{ $prestasi->judul }}" class="mt-4 w-full h-28 object-cover rounded-xl">
                    @endif
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 6. GALERI -->
    @if($galeris->isNotEmpty())
    <section id="galeri" class="bg-theme-light py-20">
        <div class="max-w-5xl mx-auto px-6">
            <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">Galeri</span>
            <h2 class="text-2xl md:text-3xl font-bold mb-10 flex items-center gap-3">Momen Kami
                        <svg class="w-7 h-7 md:w-8 md:h-8 text-theme-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($galeris as $foto)
                    <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi" class="w-full h-40 object-cover rounded-xl border border-gray-100 shadow-sm hover:scale-105 transition">
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 7. TESTIMONI -->
    @if($ekskul->testimoniss->isNotEmpty())
    <section id="testimoni" class="max-w-5xl mx-auto px-6 py-20">
        <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">Testimoni</span>
        <h2 class="text-2xl md:text-3xl font-bold mb-10 flex items-center gap-3">Kata Mereka
                        <svg class="w-7 h-7 md:w-8 md:h-8 text-theme-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($ekskul->testimoniss as $testimoni)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col">
                    <div class="text-theme-yellow text-lg mb-2 flex gap-0.5" aria-label="Rating 5 dari 5">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed flex-1 mb-4">&ldquo;{{ $testimoni->quote }}&rdquo;</p>
                    <div>
                        <p class="font-bold text-sm">{{ $testimoni->nama }}</p>
                        @if($testimoni->kelas)
                            <p class="text-[10px] text-gray-400">{{ $testimoni->kelas }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 8. FAQ -->
    @if($ekskul->faqs->isNotEmpty())
    <section id="faq" class="bg-theme-light py-20">
        <div class="max-w-3xl mx-auto px-6">
            <span class="text-[10px] font-bold text-theme-blue uppercase tracking-widest mb-2 inline-block">FAQ</span>
            <h2 class="text-2xl md:text-3xl font-bold mb-10">Pertanyaan yang Sering Ditanyakan</h2>
            <div class="space-y-4">
                @foreach($ekskul->faqs as $faq)
                    <details class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group">
                        <summary class="px-6 py-4 font-bold text-sm cursor-pointer flex items-center justify-between list-none">
                            {{ $faq->pertanyaan }}
                            <span class="text-theme-blue text-lg transition group-open:rotate-45">+</span>
                        </summary>
                        <p class="px-6 pb-4 text-xs text-gray-600 leading-relaxed">{{ $faq->jawaban }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 9. CTA -->
    <section class="bg-theme-dark text-white py-20 text-center">
        <h2 class="text-2xl md:text-3xl font-extrabold mb-4">Tertarik Bergabung?</h2>
        <p class="text-xs md:text-sm text-gray-300 max-w-md mx-auto mb-8">
            Temukan teman baru, kembangkan bakatmu, dan jadi bagian dari keluarga {{ $ekskul->nama_ekskul }}.
        </p>
        <a href="{{ route('siswa.form-daftar', $ekskul) }}" class="inline-block px-8 py-3 bg-theme-yellow hover:bg-yellow-400 text-theme-dark text-xs font-bold rounded-full transition {{ $ekskul->is_open_recruitment ? '' : 'opacity-50 pointer-events-none' }}">
            {{ $ekskul->is_open_recruitment ? 'Daftar Sekarang &rarr;' : 'Pendaftaran Ditutup' }}
        </a>
    </section>

    <!-- FOOTER -->
    <footer class="bg-theme-dark border-t border-white/10 py-10 text-center text-white text-xs">
        <p>&copy; 2026 SOUL. All rights reserved.</p>
    </footer>

</body>
</html>