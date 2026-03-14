<?php
$user = $_GET['user_id'] ?? '';
if (!$user) exit; // user_id boşsa çık

// Online dizini
$dir = __DIR__ . "/online_users";
if (!is_dir($dir)) mkdir($dir, 0755, true);

// Kullanıcının dosya adı
$file = $dir . "/{$user}.data";

// Şu anki zaman
$time = time();

// Dosyayı güvenli bir şekilde yaz (flock ile)
$fp = fopen($file, "c");
if ($fp) {
    flock($fp, LOCK_EX);    // Kilitle
    ftruncate($fp, 0);      // Önce dosyayı temizle
    fwrite($fp, $time);     // Zamanı yaz
    fflush($fp);            // Belleği temizle
    flock($fp, LOCK_UN);    // Kilidi kaldır
    fclose($fp);
}

echo "OK";
