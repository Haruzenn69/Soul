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

    protected array $seenNip = [];

    public function rules(): array
    {
        return [
            'nip' => [
                'required',
                'string',
                'regex:/^\\d+$/',
                function (string $attribute, $value, $fail): void {
                    $nip = trim((string) $value);

                    if (isset($this->seenNip[$nip])) {
                        $fail("NIP {$nip} duplikat di dalam file.");

                        return;
                    }

                    if (Pembina::where('nip', $nip)->exists() || User::where('username', $nip)->exists()) {
                        $fail("NIP {$nip} sudah terdaftar.");
                    }

                    $this->seenNip[$nip] = true;
                },
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                'distinct',
                'different:nip',
                function (string $attribute, $value, $fail): void {
                    if (User::where('username', trim((string) $value))->exists()) {
                        $fail('Username '.trim((string) $value).' sudah digunakan.');
                    }
                },
            ],
            'nama' => ['required', 'string', 'max:255'],
        ];
    }

    public function prepareForValidation(array $row): array
    {
        return [
            'nip' => trim((string) ($row['nip'] ?? '')),
            'username' => trim((string) ($row['username'] ?? '')),
            'nama' => trim((string) ($row['nama'] ?? '')),
        ];
    }

    public function model(array $row): null
    {
        $this->count++;

        $user = User::create([
            'username' => $row['username'],
            'email' => null,
            'password' => Hash::make('password'),
            'role' => 'pembina',
            'email_verified_at' => now(),
        ]);

        $user->pembina()->create([
            'nip' => $row['nip'],
            'nama' => $row['nama'],
        ]);

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
