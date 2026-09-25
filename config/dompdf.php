<?php

return [
    /*
    | Mendukung struktur deploy shared hosting:
    | - lokal  : public_path() (folder public biasa)
    | - server : souleven.my.id/public_html (webroot utama)
    */
    'public_path' => env('PDF_PUBLIC_PATH', (realpath(__DIR__ . '/../../public_html') ?: public_path())),
];