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
            'nis' => ['required', 'string', 'min:5', 'max:20', 'distinct', function (string $attribute, $value, $fail): void {
                $nis = trim((string) $value);
                if (Siswa::where('nis', $nis)->exists()) {
                    $fail("NIS {$nis} sudah terdaftar.");
                }
            }],
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'\-]+$/'],
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
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nis.string' => 'NIS harus berupa teks/angka.',
            'nis.min' => 'NIS minimal 5 karakter.',
            'nis.max' => 'NIS maksimal 20 karakter.',
            'nis.distinct' => 'NIS duplikat ditemukan dalam file. Setiap NIS harus unik.',
            'nama.required' => 'Kolom Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nama.regex' => 'Nama hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
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
        foreach (['nis', 'nama', 'email', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'no_telp', 'alamat', 'media_sosial'] as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            $clean[$key] = $value === '' ? null : $value;
        }

        // Normalize NIS: strip whitespace and handle Excel float format
        // e.g. "2603010113.0" -> "2603010113", or "2603 010113" -> "2603010113"
        if ($clean['nis'] !== null) {
            $nisRaw = $clean['nis'];
            // Remove all whitespace characters
            $nisRaw = preg_replace('/[\s\x{00A0}]+/u', '', $nisRaw);
            // Handle Excel float format (e.g., "2603010113.0")
            if (is_numeric($nisRaw)) {
                $clean['nis'] = (string) (int) (float) $nisRaw;
            } else {
                $clean['nis'] = preg_replace('/\.0+$/', '', $nisRaw);
            }
        }

        // If email is the same as a previously seen email in this import batch, set to null
        // (multiple students with identical emails - treat email as not provided)
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
            $nis = $row['nis'];
            $email = "siswa.{$nis}@soul.internal";
        }

        $user = User::create([
            'username' => null,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'email_verified_at' => now(),
            'onboarding_completed_at' => null,
        ]);

        $user->siswa()->create([
            'nis' => $row['nis'],
            'nama' => mb_strtoupper(trim((string) $row['nama'])),
            'kelas_id' => $this->kelasId,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'],
            'agama' => $row['agama'],
            'angkatan' => null,
            'email' => $row['email'] ?? null,
            'no_telp' => $row['no_telp'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'medsos' => $row['media_sosial'] ?? null,
            'jabatan' => 'siswa',
        ]);

        $this->count++;

        return null;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
