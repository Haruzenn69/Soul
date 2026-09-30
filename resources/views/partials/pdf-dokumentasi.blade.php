@php
    $dokPaths = $paths ?? [];
@endphp
@if(collect($dokPaths)->isNotEmpty())
    <div class="dokumentasi">
        @foreach($dokPaths as $dokDoc)
            @php
                $imgPath = storage_path('app/public/' . $dokDoc);
                $imgExists = file_exists($imgPath);
            @endphp
            @if($imgExists)
                @php
                    $exif = @exif_read_data($imgPath);
                    $orientation = $exif['COMPUTED']['Orientation'] ?? $exif['Orientation'] ?? null;
                    $img = @imagecreatefromstring(file_get_contents($imgPath));

                    if ($img && $orientation) {
                        $flip = [2, 4, 5, 7];
                        $rotate = [3 => 180, 6 => 270, 8 => 90];
                        if (in_array($orientation, $flip)) {
                            $img = $orientation <= 2 ? $img : imagerotate($img, $rotate[$orientation] ?? 0, 0);
                        } elseif (isset($rotate[$orientation])) {
                            $img = imagerotate($img, $rotate[$orientation], 0);
                        }
                    }

                    if ($img) {
                        ob_start();
                        imagejpeg($img, null, 90);
                        $imageData = base64_encode(ob_get_clean());
                        imagedestroy($img);
                        $src = 'data:image/jpeg;base64,' . $imageData;
                    } else {
                        $imageData = base64_encode(file_get_contents($imgPath));
                        $mime = mime_content_type($imgPath);
                        $src = 'data:' . $mime . ';base64,' . $imageData;
                    }
                @endphp
                <img src="{{ $src }}" alt="Dokumentasi">
            @endif
        @endforeach
    </div>
@endif