<?php

namespace App\Support;

class SignatureHelper
{
    /**
     * Otomatis memotong (trim) margin putih/transparan yang kosong di sekeliling tanda tangan,
     * lalu mengembalikan data URI base64 agar tanda tangan tampil optimal dan proporsional.
     */
    public static function getOptimizedBase64(?string $relativeOrFullPath): ?string
    {
        if (empty($relativeOrFullPath)) {
            return null;
        }

        $fullPath = file_exists($relativeOrFullPath)
            ? $relativeOrFullPath
            : storage_path('app/public/' . ltrim($relativeOrFullPath, '/'));

        if (! file_exists($fullPath) || ! is_file($fullPath)) {
            return null;
        }

        try {
            $info = @getimagesize($fullPath);
            if (! $info) {
                return self::rawBase64($fullPath);
            }

            $mime = $info['mime'] ?? 'image/png';

            // Jika GD tidak aktif, kembalikan base64 biasa
            if (! extension_loaded('gd')) {
                return self::rawBase64($fullPath, $mime);
            }

            $src = match ($mime) {
                'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($fullPath),
                'image/png' => @imagecreatefrompng($fullPath),
                'image/webp' => @imagecreatefromwebp($fullPath),
                default => null,
            };

            if (! $src) {
                return self::rawBase64($fullPath, $mime);
            }

            $w = imagesx($src);
            $h = imagesy($src);

            if ($w <= 10 || $h <= 10) {
                imagedestroy($src);
                return self::rawBase64($fullPath, $mime);
            }

            // Cari batas piksel non-putih / non-transparan (isi tanda tangan)
            $minX = $w;
            $minY = $h;
            $maxX = 0;
            $maxY = 0;

            // Resolusi sampling dinamis agar proses cepat
            $step = max(1, (int) floor(max($w, $h) / 600));

            for ($y = 0; $y < $h; $y += $step) {
                for ($x = 0; $x < $w; $x += $step) {
                    $rgba = imagecolorat($src, $x, $y);
                    $alpha = ($rgba & 0x7F000000) >> 24; // 0 = opaque, 127 = full transparan
                    $r = ($rgba >> 16) & 0xFF;
                    $g = ($rgba >> 8) & 0xFF;
                    $b = $rgba & 0xFF;

                    // Lewati piksel transparan atau warna putih/sangat terang (> 228)
                    if ($alpha > 100 || ($r >= 225 && $g >= 225 && $b >= 225)) {
                        continue;
                    }

                    if ($x < $minX) $minX = $x;
                    if ($x > $maxX) $maxX = $x;
                    if ($y < $minY) $minY = $y;
                    if ($y > $maxY) $maxY = $y;
                }
            }

            // Jika tanda tangan terdeteksi dan area coretannya valid
            if ($minX < $maxX && $minY < $maxY) {
                $pad = max(6, (int) round(min($w, $h) * 0.02));
                $minX = max(0, $minX - $pad);
                $minY = max(0, $minY - $pad);
                $maxX = min($w - 1, $maxX + $pad);
                $maxY = min($h - 1, $maxY + $pad);

                $cropW = $maxX - $minX + 1;
                $cropH = $maxY - $minY + 1;

                $cropped = imagecreatetruecolor($cropW, $cropH);
                imagealphablending($cropped, false);
                imagesavealpha($cropped, true);
                $trans = imagecolorallocatealpha($cropped, 255, 255, 255, 127);
                imagefilledrectangle($cropped, 0, 0, $cropW, $cropH, $trans);

                imagecopy($cropped, $src, 0, 0, $minX, $minY, $cropW, $cropH);

                ob_start();
                imagepng($cropped, null, 7);
                $pngData = ob_get_clean();

                imagedestroy($src);
                imagedestroy($cropped);

                return 'data:image/png;base64,' . base64_encode($pngData);
            }

            imagedestroy($src);
            return self::rawBase64($fullPath, $mime);
        } catch (\Throwable) {
            return self::rawBase64($fullPath);
        }
    }

    private static function rawBase64(string $path, ?string $mime = null): string
    {
        $mime = $mime ?: (mime_content_type($path) ?: 'image/png');
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
