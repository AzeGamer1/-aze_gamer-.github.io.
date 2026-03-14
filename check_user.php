<?php
header('Content-Type: application/json');

$device_id = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';

if ($device_id === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Eksik parametre.'));
    exit;
}

$dataFile = 'users.json';
$users = array();

if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $users = json_decode($json, true);
    if (!is_array($users)) $users = array();
}

// Eğer kullanıcı varsa username döndür
if (isset($users[$device_id])) {
    echo json_encode(array(
        'status' => 'ok',
        'username' => $users[$device_id]
    ));
} else {
    echo json_encode(array(
        'status' => 'ok',
        'username' => null
    ));
}
