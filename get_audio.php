<?php
$dir = "audio/queue/";
$files = glob($dir."*.pcm");
sort($files);

if (!$files) {
    http_response_code(204);
    exit;
}

// Sıradakı fayl
$f = $files[0];

// Faylı oxu və sonra sil
header("Content-Type: application/octet-stream");
readfile($f);
unlink($f);
?>