<?php
// JSON verisini içeren dosyanın yolu
$filename = 'LoginData.data';

// Dosyayı oku ve JSON verisini çözümle
$json_data = file_get_contents($filename);
$data = json_decode($json_data, true);

// Hata kontrolü
if ($data === null) {
    die('JSON verisi işlenirken bir hata oluştu.');
}

// JSON verisini döndür
header('Content-Type: application/json');
echo json_encode($data);
?>