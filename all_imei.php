<?php
header("Content-Type: text/plain; charset=UTF-8");

$file = "imei.txt";

if (!file_exists($file)) {
    exit; // dosya yoksa boş cevap
}

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $imei) {
    echo trim($imei) . "\n";
}