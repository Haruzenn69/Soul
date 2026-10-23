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

    protected array $seenEmails = [];

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'min:10', 'max:20', 'distinct', function (string $attribute, $value, $fail): void {
                $nip = trim((string) $value);
                if (Pembina::where('nip', $nip)->exists()) {
                    $fail("NIP {$nip} sudah terdaftar.");
                }
            }],
            'nama_lengkap' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'\\-]+$/'],
            'email' => ['nullable', 'email', 'max:255', function (string $attribute, $value, $fail): void {
                if ($value && User::where('email', trim((string) $value))->exists()) {
                    $fail('Email '.trim((string) $value).' sudah digunakan.');
                }
            }],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date'],
            'agama' => ['required', 'string', 'max:50', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu'],
            'no_telp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string'],
            'media_sosial' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nip.required' => 'Kolom NIP wajib diisi.',
            'nip.string' => 'NIP harus berupa teks/angka.',
            'nip.min' => 'NIP minimal 10 karakter.',
            'nip.max' => 'NIP maksimal 20 karakter.',
            'nip.distinct' => 'NIP duplikat ditemukan dalam file. Setiap NIP harus unik.',
            'nama_lengkap.required' => 'Kolom Nama Lengkap wajib diisi.',
            'nama_lengkap.string' => 'Nama Lengkap harus berupa teks.',
            'nama_lengkap.max' => 'Nama Lengkap maksimal 255 karakter.',
            'nama_lengkap.regex' => 'Nama Lengkap hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
            'email.email' => 'Format Email tidak valid. Email harus mengandung tanda @.',
            'email.max' => 'Email maksimal 255 karakter.',
            'jenis_kelamin.required' => 'Kolom Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Jenis Kelamin harus "Laki-laki" atau "Perempuan".',
            'tempat_lahir.string' => 'Tempat Lahir harus berupa teks.',
            'tempat_lahir.max' => 'Tempat Lahir maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Kolom Tanggal Lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal Lahir harus berupa tanggal yang valid (contoh: 26-06-2008).',
            'agama.required' => 'Kolom Agama wajib diisi.',
            'agama.string' => 'Agama harus berupa teks.',
            'agama.max' => 'Agama maksimal 50 karakter.',
            'agama.in' => 'Agama tidak valid. Pilih dari: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu.',
            'no_telp.string' => 'No. Telp harus berupa teks.',
            'no_telp.max' => 'No. Telp maksimal 25 karakter.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'media_sosial.string' => 'Media Sosial harus berupa teks.',
            'media_sosial.max' => 'Media Sosial maksimal 255 karakter.',
        ];
    }

    public function prepareForValidation(array $row): array
    {
        $clean = [];
        foreach (['nip', 'nama_lengkap', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'no_telp', 'alamat', 'media_sosial'] as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            $clean[$key] = $value === '' ? null : $value;
        }

        // Normalize NIP: strip all whitespace and handle Excel float format
        // e.g. "19800101 200012 1 001" -> "198001012000121001", or "198001012000121001.0" -> "198001012000121001"
        if ($clean['nip'] !== null) {
            $nipRaw = $clean['nip'];
            // Remove all whitespace characters (spaces, tabs, non-breaking spaces)
            $nipRaw = preg_replace('/[\s\x{00A0}]+/u', '', $nipRaw);
            // Handle Excel float format (e.g., "198001012000121001.0")
            if (is_numeric($nipRaw)) {
                $nipRaw = (string) (int) (float) $nipRaw;
            } else {
                $nipRaw = preg_replace('/\.0+$/', '', $nipRaw);
            }
            $clean['nip'] = $nipRaw;
        }

        // If email is duplicate within this batch, set to null
        // (will get a placeholder email generated in model())
        if ($clean['email'] !== null) {
            $email = strtolower(trim($clean['email']));
            if (in_array($email, $this->seenEmails, true)) {
                $clean['email'] = null;
            } else {
                $this->seenEmails[] = $email;
            }
        }

        $clean['tempat_lahir'] = SpreadsheetProfileValue::placeOfBirth($clean['tempat_lahir']);
        $clean['agama'] = SpreadsheetProfileValue::religion($clean['agama']);
        $clean['jenis_kelamin'] = SpreadsheetProfileValue::gender($clean['jenis_kelamin']);
        $clean['tanggal_lahir'] = SpreadsheetProfileValue::date($clean['tanggal_lahir']);

        return $clean;
    }

    public function model(array $row): null
    {
        // If email is null (not provided or duplicate in batch), generate a placeholder
        $email = $row['email'] ?? null;
        if (! $email) {
            $nip = $row['nip'];
            $email = "pembina.{$nip}@soul.internal";
        }

        $user = User::create([
            'username' => null,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'pembina',
            'email_verified_at' => now(),
            'onboarding_completed_at' => null,
        ]);

        $user->pembina()->create([
            'nip' => $row['nip'],
            'nama' => mb_strtoupper(trim((string) $row['nama_lengkap'])),
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'],
            'agama' => $row['agama'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'email' => $row['email'] ?? null,
            'no_telp' => $row['no_telp'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'medsos' => $row['media_sosial'] ?? null,
        ]);

        $this->count++;

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
