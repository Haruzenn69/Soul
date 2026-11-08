<?php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views/kesiswaan'));
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $f) {
    $c = file_get_contents($f);
    $count = substr_count($c, '<svg');
    if ($count > 0) {
        echo "$f: $count svgs\n";
    }
}

