<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiswaImport implements SkipsEmptyRows, SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public int $count = 0;

    protected array $seenNis = [];

    protected array $seenEmails = [];

    public function __construct(protected int $kelasId) {}

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'digits:10', 'distinct', function (string $attribute, $value, $fail): void {
                $nis = trim((string) $value);
                if (Siswa::where('nis', $nis)->exists() || User::where('username', $nis)->exists()) {
                    $fail("NIS {$nis} sudah terdaftar.");
                }
            }],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'distinct', function (string $attribute, $value, $fail): void {
                if ($value && User::where('email', trim((string) $value))->exists()) {
                    $fail('Email '.trim((string) $value).' sudah digunakan.');
                }
            }],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['required', 'string', 'max:50'],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['required', 'string'],
            'media_sosial' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        $clean = [];
        foreach (['nis', 'nama', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'no_telp', 'alamat', 'media_sosial'] as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            $clean[$key] = $value === '' ? null : $value;
        }
        $clean['jenis_kelamin'] = SpreadsheetProfileValue::gender($clean['jenis_kelamin']);
        $clean['tanggal_lahir'] = SpreadsheetProfileValue::date($clean['tanggal_lahir']);

        return $clean;
    }

    public function model(array $row): null
    {
        $user = User::create([
            'username' => $row['nis'],
            'email' => $row['email'],
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);

        $user->siswa()->create([
            'nis' => $row['nis'], 'nama' => $row['nama'], 'kelas_id' => $this->kelasId,
            'jenis_kelamin' => $row['jenis_kelamin'], 'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'], 'agama' => $row['agama'],
            'angkatan' => null, 'email' => $row['email'] ?? null, 'no_telp' => $row['no_telp'] ?? null,
            'alamat' => $row['alamat'] ?? null, 'medsos' => $row['media_sosial'] ?? null, 'jabatan' => 'siswa',
        ]);

        $this->count++;

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
