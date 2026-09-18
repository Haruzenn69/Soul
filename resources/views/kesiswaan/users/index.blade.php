@extends('layouts.kesiswaan')

@section('title', 'Akun Pengguna')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Akun Pengguna</h1>
            <p class="text-xs text-gray-400 mt-1">Buat, ubah role, reset password, dan hapus akun. Password default: <span class="font-bold text-theme-dark">password</span></p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('kesiswaan.users.import') }}"
               class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-full shadow-lg shadow-emerald-500/20 transition flex items-center gap-2">
                <span>⬆</span> Import Excel
            </a>
            <a href="{{ route('kesiswaan.users.create') }}"
               class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2">
                <span>+</span> Buat Akun
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if (session('importErrors'))
        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl text-xs font-semibold">
            <p class="mb-2 font-bold">⚠ Beberapa baris gagal diimpor:</p>
            <ul class="list-disc list-inside space-y-1 max-h-48 overflow-y-auto">
                @foreach (session('importErrors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter -->
    <form method="GET" action="{{ route('kesiswaan.users.index') }}" class="flex flex-wrap gap-3 items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari username, email, nama..."
               class="flex-1 min-w-[200px] px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
        <select name="role" class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <option value="">Semua Role</option>
            @foreach (['admin', 'kesiswaan', 'pembina', 'siswa'] as $r)
                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-theme-dark text-white font-bold text-xs rounded-2xl hover:bg-black transition">Filter</button>
    </form>

    <!-- Tabel Akun -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="card-table w-full text-left">
            <thead>
                <tr class="text-[11px] font-bold text-gray-400 tracking-wider uppercase border-b border-gray-100">
                    <th class="py-3 px-2">Pengguna</th>
                    <th class="py-3 px-2">Username</th>
                    <th class="py-3 px-2">Email</th>
                    <th class="py-3 px-2">Role</th>
                    <th class="py-3 px-2">Detail</th>
                    <th class="py-3 px-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50/60 transition text-xs">
                        <td class="py-3.5 px-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full {{ $user->role === 'pembina' ? 'bg-theme-blue' : ($user->role === 'kesiswaan' ? 'bg-theme-yellow' : 'bg-emerald-500') }} text-white font-bold flex items-center justify-center text-[10px] uppercase shrink-0">
                                    {{ strtoupper(substr($user->username ?? ($user->siswa?->nama ?? $user->pembina?->nama ?? '--'), 0, 2)) }}
                                </div>
                                <span class="font-bold">{{ $user->siswa?->nama ?? $user->pembina?->nama ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-2 font-medium">{{ $user->username ?? '(belum diisi)' }}</td>
                        <td class="py-3.5 px-2 text-gray-500">{{ $user->email }}</td>
                        <td class="py-3.5 px-2">
                            <span class="px-3 py-1 rounded-full font-bold text-[11px]
                                {{ $user->role === 'admin' ? 'bg-purple-50 text-purple-600' : '' }}
                                {{ $user->role === 'kesiswaan' ? 'bg-yellow-50 text-yellow-600' : '' }}
                                {{ $user->role === 'pembina' ? 'bg-blue-50 text-theme-blue' : '' }}
                                {{ $user->role === 'siswa' ? 'bg-emerald-50 text-emerald-600' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if ($user->role === 'siswa' && $user->siswa?->jabatan === 'ketua')
                                <span class="ml-1 px-3 py-1 rounded-full font-bold text-[11px] bg-theme-dark text-theme-yellow">Ketua</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-2 text-gray-400">
                            @if ($user->siswa)
                                NIS {{ $user->siswa->nis }} Â· {{ $user->siswa->kelas?->nama ?? '-' }}
                            @elseif ($user->pembina)
                                NIP {{ $user->pembina->nip }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3.5 px-2">
                            <div class="flex gap-2 justify-end items-center">
                                @if ($user->role === 'admin' && auth()->user()->role !== 'admin')
                                    <span class="text-[10px] text-gray-400 italic px-3">ðŸ”’ Dikelola Admin</span>
                                @else
                                    <a href="{{ route('kesiswaan.users.edit', $user) }}"
                                       class="px-3 py-1.5 bg-blue-50 text-theme-blue font-bold rounded-full hover:bg-blue-100 transition">Edit</a>
                                    <form action="{{ route('kesiswaan.users.reset-password', $user) }}" method="POST"
                                          onsubmit="return confirm('Reset password akun {{ $user->username ?? '(belum diisi)' }} ke &quot;password&quot;?')">
                                        @csrf
                                        <button type="submit" title="Reset password"
                                                class="px-3 py-1.5 bg-amber-50 text-amber-600 font-bold rounded-full hover:bg-amber-100 transition">Reset PW</button>
                                    </form>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('kesiswaan.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Hapus akun {{ $user->username ?? '(belum diisi)' }}? Tindakan ini permanen!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-500 font-bold rounded-full hover:bg-red-100 transition">Hapus</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-gray-400 text-xs">Belum ada akun yang cocok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
