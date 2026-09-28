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
                    fontFamily: {
                        sans: ['Figtree', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        bg: '#0e0e27',
                        fg: '#f0f0f5',
                        primary: '#00d4aa',
                        secondary: '#ff6b9d',
                        card: '#1b1b3b',
                        muted: '#6b7280',
                        surface: '#16162a',
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
</head>
<body class="bg-bg fg-fg antialiased">

    <!-- NAVBAR -->
    <header class="px-6 md:px-8 py-4 flex items-center justify-between border-b border-border sticky top-0 bg-bg/80 backdrop-blur-md z-50">
        <a href="/" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm">S</div>
            <span class="font-extrabold tracking-tight text-lg fg uppercase">SOUL</span>
        </a>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-primary hover:bg-secondary/20 text-primary fg text-xs font-semibold rounded-full transition">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-5 py-2 bg-card/50 hover:bg-card/70 fg text-xs font-semibold rounded-full transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-card/50 hover:bg-card/70 fg text-xs font-semibold rounded-full transition">Masuk &rarr;</a>
            @endauth
        </div>
    </header>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium rounded text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- 1. HERO -->
    <section class="relative overflow-hidden py-24 md:py-32">
        @if($ekskul->cover)
            <img src="{{ asset('storage/' . $ekskul->cover) }}" alt="{{ $ekskul->nama_ekskul }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-bg"></div>
        @endif
        <div class="relative z-10 max-w-6xl mx-auto px-6 py-16 text-center">
            @if($ekskul->logo)
                <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-24 h-24 mx-auto mb-6 object-contain border-primary/50">
            @else
                <div class="w-24 h-24 mx-auto mb-6 rounded-xl bg-primary/10 border border-primary/50 flex items-center justify-center text-primary font-extrabold text-4xl">
                    {{ substr($ekskul->nama_ekskul, 0, 1) }}
                </div>
            @endif
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 fg">{{ $ekskul->nama_ekskul }}</h1>
            @if($ekskul->tagline)
                <p class="text-primary/80 font-medium text-sm mb-8 max-w-xl mx-auto">{{ $ekskul->tagline }}</p>
            @endif
            <p class="text-muted/60 text-sm mb-8 max-w-xl mx-auto leading-relaxed">{{ $ekskul->deskripsi }}</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center sm:gap-4">
                @if($ekskul->is_open_recruitment)
                    <a href="{{ route('siswa.form-daftar', $ekskul) }}" class="px-8 py-3 bg-primary text-bg font-bold rounded-full text-sm tracking-wider hover:opacity-90 transition">
                        Gabung Ekskul
                    </a>
                @else
                    <span class="px-8 py-3 bg-card/50 text-muted/50 font-bold rounded-full text-sm tracking-wider disabled opacity-50 pointer-events-none">
                        Pendaftaran Ditutup
                    </span>
                @endif
            </div>
        </div>
    </section>

    <!-- 2. QUICK INFO -->
    <section class="py-12 md:py-16 border-t border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-card border border-border p-6">
                    <p class="text-sm font-medium uppercase tracking-wider mb-2 fg/60">Jadwal</p>
                    <p class="text-2xl font-bold fg">{{ $ekskul->jadwal ?? '-' }}</p>
                </div>
                <div class="bg-card border border-border p-6">
                    <p class="text-sm font-medium uppercase tracking-wider mb-2 fg/60">Anggota</p>
                    <p class="text-2xl font-bold fg">{{ $totalAnggota }} Anggota</p>
                </div>
                <div class="bg-card border border-border p-6">
                    <p class="text-sm font-medium uppercase tracking-wider mb-2 fg/60">Pembina</p>
                    <p class="text-xl font-bold fg">{{ $ekskul->pembina->nama ?? '-' }}</p>
                </div>
                <div class="bg-card border border-border p-6">
                    <p class="text-sm font-medium uppercase tracking-wider mb-2 fg/60">Pelatih</p>
                    <p class="text-xl font-bold fg">{{ $ekskul->pelatih->nama ?? '-' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. TENTANG -->
    @if($ekskul->tujuan || $ekskul->deskripsi)
    <section id="tentang" class="py-16 md:py-20 border-b border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Kami berkenalan dulu, yuk!</h2>
            <p class="text-muted/60 text-lg mb-6 leading-relaxed">{{ $ekskul->deskripsi }}</p>
            @if($ekskul->tujuan)
                <p class="text-muted/60 text-base mb-4 leading-relaxed">{{ $ekskul->tujuan }}</p>
            @endif
        </div>
    </section>
    @endif

    <!-- 4. KEGIATAN -->
    @if($ekskul->kegiatans->isNotEmpty())
    <section id="kegiatan" class="py-16 md:py-20 border-b border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Apa yang Kami Lakukan?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ekskul->kegiatans as $kegiatan)
                <div class="bg-card border border-border p-6 hover:border-primary transition">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold fg">{{ $kegiatan->materi }}</h3>
                        <p class="text-sm fg/60">{{ $kegiatan->tanggal_kegiatan->translatedFormat('d F Y') }}</p>
                    </div>
                    @if($kegiatan->deskripsi)
                        <p class="text-muted/60 text-sm leading-relaxed">{{ \Illuminate\Support\Str::limit($kegiatan->deskripsi, 100) }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 5. PRESTASI -->
    @if($ekskul->prestasis->isNotEmpty())
    <section id="prestasi" class="py-16 md:py-20 border-b border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Kebanggaan Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ekskul->prestasis as $prestasi)
                <div class="bg-card border border-border p-6 hover:border-primary transition">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold fg">{{ $prestasi->judul }}</h3>
                        <p class="text-sm fg/60">{{ $prestasi->tahun ?? '-' }}</p>
                    </div>
                    @if($prestasi->kategori)
                        <p class="text-muted/60 text-xs">{{ $prestasi->kategori }}</p>
                    @endif
                    @if($prestasi->foto)
                        <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="{{ $prestasi->judul }}" class="mt-4 w-full h-32 object-cover rounded border-border/20">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 6. GALERI -->
    @if($galeris->isNotEmpty())
    <section id="galeri" class="py-16 md:py-20 bg-card border-t border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Momen Kami</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($galeris as $foto)
                    <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi" class="h-40 object-cover rounded border-border/20 transition">
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 7. TESTIMONI -->
    @if($ekskul->testimoniss->isNotEmpty())
    <section id="testimoni" class="py-16 md:py-20 border-b border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Kata Mereka</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ekskul->testimoniss as $testimoni)
                    <div class="bg-card border border-border p-6 flex flex-col">
                        <p class="text-primary/80 text-lg mb-4 flex gap-0.5" aria-label="Rating">{{ str_repeat('<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>', 5); }}</p>
                        <p class="text-muted/60 flex-1 mb-4 leading-relaxed">&ldquo;{{ $testimoni->quote }}&rdquo;</p>
                        <div class="text-primary font-bold text-sm mb-2">{{ $testimoni->nama }}</div>
                        @if($testimoni->kelas)
                            <p class="text-muted/60 text-xs">{{ $testimoni->kelas }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 8. FAQ -->
    @if($ekskul->faqs->isNotEmpty())
    <section id="faq" class="py-16 md:py-20 bg-card border-b border-border/20">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-6 fg">Pertanyaan yang Sering Ditanyakan</h2>
            <div class="space-y-4">
                @foreach($ekskul->faqs as $faq)
                    <details class="bg-card border border-border p-6 overflow-hidden">
                        <summary class="px-6 py-4 font-bold text-sm cursor-pointer flex items-center justify-between">
                            {{ $faq->pertanyaan }}
                            <span class="text-primary/80 text-lg transition">+</span>
                        </summary>
                        <p class="px-6 pb-4 text-muted/60 leading-relaxed">{{ $faq->jawaban }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 9. TESTIMONI & FAQ FORM -->
    @auth
        @if(auth()->user()->role !== 'kesiswaan')
        <section class="py-16 md:py-20 max-w-2xl mx-auto px-6">
            @if($hasSubmittedTestimoni)
                <p class="text-muted/50 text-center">✅ Kamu sudah mengirim testimoni untuk ekskul ini.</p>
            @else
                <form method="POST" action="{{ route('ekskul.testimoni.store', $ekskul) }}" class="bg-card border border-border p-6">
                    @csrf
                    <h3 class="font-bold text-sm mb-3">Bagikan pengalamanmu</h3>
                    @error('quote')
                        <p class="text-primary/80 text-xs mb-2">{{ $message }}</p>
                    @enderror
                    <textarea name="quote" rows="3" required maxlength="2000" placeholder="Tulis testimoni singkatmu untuk ekskul ini..."
                        class="w-full px-4 py-3 rounded border border-muted/50 text-sm focus:outline-none focus:border-primary mb-4"></textarea>
                    <button type="submit" class="w-full py-3 bg-primary text-bg font-bold rounded-full text-sm transition">Kirim Testimoni</button>
                    <p class="text-muted/60 text-xs">Nama & kelas diambil dari akunmu. Menunggu persetujuan ketua sebelum tampil.</p>
                </form>
            @endif
        </section>
        @endif
    @endauth

    <!-- 10. FAQ FORM -->
    @auth
        @if(auth()->user()->role !== 'kesiswaan')
        <section class="py-16 md:py-20 max-w-3xl mx-auto px-6">
            <form method="POST" action="{{ route('ekskul.faq.store', $ekskul) }}" class="bg-card border border-border p-6">
                @csrf
                <h3 class="font-bold text-sm mb-3">Masih penasaran? Tanyakan ke ketua</h3>
                @error('pertanyaan')
                    <p class="text-primary/80 text-xs mb-2">{{ $message }}</p>
                @enderror
                <input type="text" name="pertanyaan" required maxlength="255" placeholder="Tulis pertanyaanmu tentang ekskul ini..."
                    class="w-full px-4 py-3 rounded border border-muted/50 text-sm focus:outline-none focus:border-primary mb-4">
                <button type="submit" class="w-full py-3 bg-primary/20 text-primary fg font-bold rounded-full text-sm transition">Ajukan Pertanyaan</button>
                <p class="text-muted/60 text-xs">Pertanyaanmu akan dijawab ketua ekskul dan tampil jika dijawab.</p>
            </form>
        </section>
        @endif
    @endauth

    <!-- CTA -->
    <section class="py-24 md:py-32 bg-primary/10">
        <div class="max-w-6xl mx-auto px-6 text-center">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 fg">Tertarik Bergabung?</h2>
            <p class="text-muted/60 text-lg max-w-xl mx-auto mb-8 leading-relaxed">
                Temukan teman baru, kembangkan bakatmu, dan jadi bagian dari keluarga {{ $ekskul->nama_ekskul }}.
            </p>
            @if($ekskul->is_open_recruitment)
                <a href="{{ route('siswa.form-daftar', $ekskul) }}" class="px-10 py-3 bg-primary text-bg font-bold rounded-full text-sm tracking-wider hover:opacity-90 transition">
                    Gabung Ekskul
                </a>
            @else
                <span class="px-10 py-3 bg-card/50 text-muted/50 font-bold rounded-full text-sm tracking-wider disabled opacity-50 pointer-events-none">
                    Pendaftaran Ditutup
                </span>
            @endif
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-12 bg-bg border-t border-border/20">
        <div class="max-w-7xl mx-auto px-6 text-center text-muted/50 text-xs">
            <p>&copy; 2026 SOUL. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
