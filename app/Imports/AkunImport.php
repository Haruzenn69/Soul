<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AkunImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected int $created = 0;

    protected array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = $row->map(fn ($value) => trim((string) $value ?: ''))->filter(fn ($value) => $value !== '');
            if ($row->isEmpty()) {
                continue;
            }

            try {
                $this->importRow($row);
            } catch (\Throwable $e) {
                $this->errors[] = '['.($row['email'] ?? $row['nis'] ?? $row['nip'] ?? '').'] '.$e->getMessage();
            }
        }
    }

    protected function importRow(Collection $row): void
    {
        $email = $row['email'] ?? '';

        // Jika role diberikan di file, gunakan; kalau tidak, deteksi dari kolom
        $role = strtolower($row['role'] ?? '');
        if (!in_array($role, ['siswa', 'pembina', 'kesiswaan'], true)) {
            $role = $row->has('nis') ? 'siswa' : ($row->has('nip') ? 'pembina' : 'kesiswaan');
        }

        if ($role === 'siswa') {
            $this->importSiswa($row, $email);
        } elseif ($role === 'pembina') {
            $this->importPembina($row, $email);
        } else {
            $this->importKesiswaan($row, $email);
        }
    }

    protected function importSiswa(Collection $row, string $email): void
    {
        $nis = $row['nis'] ?? throw new \RuntimeException('NIS wajib diisi.');
        $nama = $row['nama'] ?? throw new \RuntimeException('Nama wajib diisi.');

        $namaKelas = trim((string) ($row['kelas'] ?? ''));
        if ($namaKelas === '') {
            throw new \RuntimeException('Kelas wajib diisi.');
        }

        $kelas = Kelas::where('nama', $namaKelas)->value('id')
            ?? Kelas::where('nama', 'like', '%'.$namaKelas.'%')->value('id');

        if (!$kelas) {
            $available = Kelas::orderBy('nama')->pluck('nama')->implode(', ');
            throw new \RuntimeException("Kelas '{$namaKelas}' tidak ditemukan. Kelas tersedia: {$available}");
        }

        $jabatan = strtolower($row['jabatan'] ?? 'siswa');
        if (!in_array($jabatan, ['siswa', 'anggota', 'ketua'], true)) {
            throw new \RuntimeException("Jabatan '{$row['jabatan']}' tidak valid.");
        }

        $jenisKelamin = strtolower($row['jenis_kelamin'] ?? '');
        if (!in_array($jenisKelamin, ['laki-laki', 'perempuan', 'laki laki'], true)) {
            throw new \RuntimeException("Jenis kelamin '{$row['jenis_kelamin']}' tidak valid.");
        }

        $jenisKelamin = $jenisKelamin === 'laki laki' ? 'laki-laki' : $jenisKelamin;

        $nis = (string) $nis;
        $email = $email ?: strtolower($nis).'@soul.test';

        $password = $row['password'] ?? 'password';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Email tidak valid.');
        }

        if (User::where('email', $email)->exists()) {
            throw new \RuntimeException("Email {$email} sudah dipakai.");
        }
        if (Siswa::where('nis', $nis)->exists()) {
            throw new \RuntimeException("NIS {$nis} sudah terdaftar.");
        }

        $user = User::create([
            'username'          => null,
            'email'             => $email,
            'password'          => Hash::make($password),
            'role'              => 'siswa',
            'email_verified_at' => now(),
        ]);

        $user->siswa()->create([
            'nis'          => $nis,
            'nama'         => $nama,
            'kelas_id'     => $kelas,
            'jenis_kelamin'=> $jenisKelamin,
            'jabatan'      => $jabatan,
        ]);

        $this->created++;
    }

    protected function importPembina(Collection $row, string $email): void
    {
        $nip  = $row['nip'] ?? throw new \RuntimeException('NIP wajib diisi.');
        $nama = $row['nama_pembina'] ?? $row['nama'] ?? throw new \RuntimeException('Nama pembina wajib diisi.');

        $jenisKelamin = strtolower($row['jenis_kelamin'] ?? '');
        if (!in_array($jenisKelamin, ['laki-laki', 'perempuan', 'laki laki'], true)) {
            throw new \RuntimeException("Jenis kelamin '{$row['jenis_kelamin']}' tidak valid.");
        }
        $jenisKelamin = $jenisKelamin === 'laki laki' ? 'laki-laki' : $jenisKelamin;

        $nip = (string) $nip;
        $email = $email ?: strtolower($nip).'@soul.test';
        $password = $row['password'] ?? 'password';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Email tidak valid.');
        }
        if (User::where('email', $email)->exists()) {
            throw new \RuntimeException("Email {$email} sudah dipakai.");
        }
        if (Pembina::where('nip', $nip)->exists()) {
            throw new \RuntimeException("NIP {$nip} sudah terdaftar.");
        }

        $user = User::create([
            'username'          => null,
            'email'             => $email,
            'password'          => Hash::make($password),
            'role'              => 'pembina',
            'email_verified_at' => now(),
        ]);

        $user->pembina()->create([
            'nip'          => $nip,
            'nama'         => $nama,
            'jenis_kelamin'=> $jenisKelamin,
        ]);

        $this->created++;
    }

    protected function importKesiswaan(Collection $row, string $email): void
    {
        $nama = $row['nama'] ?? '';
        $username = Str::slug($nama ?: 'kesiswaan').random_int(10, 99);
        $email = $email ?: $username.'@soul.test';
        $password = $row['password'] ?? 'password';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Email tidak valid.');
        }
        if (User::where('email', $email)->exists()) {
            throw new \RuntimeException("Email {$email} sudah dipakai.");
        }

        User::create([
            'username'          => $username,
            'email'             => $email,
            'password'          => Hash::make($password),
            'role'              => 'kesiswaan',
            'email_verified_at' => now(),
        ]);

        $this->created++;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email'],
        ];
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}