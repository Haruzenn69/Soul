<?php

namespace App\Imports;

use App\Models\Pembina;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PembinaImport implements SkipsEmptyRows, SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use SkipsFailures;

    public int $count = 0;

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'digits:18', 'distinct', function (string $attribute, $value, $fail): void {
                $nip = trim((string) $value);
                if (Pembina::where('nip', $nip)->exists() || User::where('username', $nip)->exists()) {
                    $fail("NIP {$nip} sudah terdaftar.");
                }
            }],
            'username' => ['nullable', 'string', 'max:255', 'distinct', 'different:nip', function (string $attribute, $value, $fail): void {
                if ($value && User::where('username', trim((string) $value))->exists()) {
                    $fail('Username '.trim((string) $value).' sudah digunakan.');
                }
            }],
            'nama_lengkap' => ['required', 'string', 'max:255'],
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
        foreach (['nip', 'username', 'nama_lengkap', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'no_telp', 'alamat', 'media_sosial'] as $key) {
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
            'username' => $row['username'] ?? null,
            'email' => $row['email'] ?? null,
            'password' => Hash::make('password'),
            'role' => 'pembina',
            'email_verified_at' => now(),
        ]);

        $user->pembina()->create([
            'nip' => $row['nip'], 'nama' => $row['nama_lengkap'], 'tempat_lahir' => $row['tempat_lahir'],
            'tanggal_lahir' => $row['tanggal_lahir'], 'agama' => $row['agama'],
            'jenis_kelamin' => $row['jenis_kelamin'], 'email' => $row['email'] ?? null,
            'no_telp' => $row['no_telp'] ?? null, 'alamat' => $row['alamat'], 'medsos' => $row['media_sosial'] ?? null,
        ]);

        $this->count++;

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
