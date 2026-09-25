{{-- Ringkasan Rekap Bulan Ini (dipakai seragam oleh ketua & pembina) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 animate-fade-up" style="animation-delay: .15s">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-4 md:p-5 shadow-lg shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Hadir</p>
                <h3 class="text-xl md:text-3xl font-extrabold text-emerald-600 mt-1 md:mt-1.5">{{ $totalHadir }}</h3>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-emerald-200">H</div>
        </div>
        <p class="text-[9px] md:text-[11px] font-semibold text-emerald-700 mt-0.5 md:mt-1">Kehadiran</p>
    </div>

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-4 md:p-5 shadow-lg shadow-amber-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .2s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Izin</p>
                <h3 class="text-xl md:text-3xl font-extrabold text-amber-600 mt-1 md:mt-1.5">{{ $totalIzin }}</h3>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-amber-200">I</div>
        </div>
        <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Perizinan</p>
    </div>

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-50 to-white border border-rose-100 p-4 md:p-5 shadow-lg shadow-rose-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .25s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Sakit</p>
                <h3 class="text-xl md:text-3xl font-extrabold text-red-600 mt-1 md:mt-1.5">{{ $totalSakit }}</h3>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-rose-400 to-red-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-rose-200">S</div>
        </div>
        <p class="text-[9px] md:text-[11px] font-semibold text-rose-700 mt-0.5 md:mt-1">Keterangan</p>
    </div>

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-4 md:p-5 shadow-lg shadow-sky-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .3s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Alpha</p>
                <h3 class="text-xl md:text-3xl font-extrabold text-sky-700 mt-1 md:mt-1.5">{{ $totalAlpha }}</h3>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-sky-200">A</div>
        </div>
        <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Tanpa Keterangan</p>
    </div>
</div>