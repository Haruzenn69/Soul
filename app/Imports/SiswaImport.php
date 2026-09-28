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

    public function __construct(protected int $kelasId) {}

    public function rules(): array
    {
        return [
            'nis' => [
                'required',
                'string',
                'regex:/^\\d+$/',
                function (string $attribute, $value, $fail): void {
                    $nis = trim((string) $value);

                    if (isset($this->seenNis[$nis])) {
                        $fail("NIS {$nis} duplikat di dalam file.");

                        return;
                    }

                    if (Siswa::where('nis', $nis)->exists() || User::where('username', $nis)->exists()) {
                        $fail("NIS {$nis} sudah terdaftar.");
                    }

                    $this->seenNis[$nis] = true;
                },
            ],
            'jabatan' => ['required', 'string', 'in:siswa,ketua'],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        return [
            'nis' => trim((string) ($row['nis'] ?? '')),
            'nama' => trim((string) ($row['nama'] ?? '')),
            'jabatan' => strtolower(trim((string) ($row['jabatan'] ?? ''))),
        ];
    }

    public function model(array $row): null
    {
        $this->count++;

        $user = User::create([
            'username' => $row['nis'],
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'email_verified_at' => now(),
        ]);

        $user->siswa()->create([
            'nis' => $row['nis'],
            'nama' => $row['nama'],
            'kelas_id' => $this->kelasId,
            'jabatan' => $row['jabatan'],
        ]);

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
