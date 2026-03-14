<?php
header('Content-Type: application/json');

// Basit güvenlik: sadece POST kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('status' => 'error', 'message' => 'Sadece POST metodu izinli.'));
    exit;
}

// POST verilerini al
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$device_id = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';

if ($username === '' || $device_id === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Eksik parametre.'));
    exit;
}

// Basit veri saklama için dosya tabanlı sistem
// İstersen bunu MySQL/MariaDB ile değiştirebilirsin
$dataFile = 'users.json';
$users = array();

// Dosya varsa oku
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $users = json_decode($json, true);
    if (!is_array($users)) $users = array();
}

// Device ID mevcut mu kontrol et
if (isset($users[$device_id])) {
    // Mevcut kullanıcı, username'i güncellemek istersen burada yapabilirsin
    $response = array(
        'status' => 'ok',
        'message' => 'Device zaten kayıtlı.',
        'username' => $users[$device_id]
    );
} else {
    // Yeni kullanıcı, kaydet
    $users[$device_id] = $username;
    file_put_contents($dataFile, json_encode($users, JSON_PRETTY_PRINT));
    $response = array(
        'status' => 'ok',
        'message' => 'Yeni kullanıcı kaydedildi.',
        'username' => $username
    );
}

// Yanıt gönder
echo json_encode($response);
