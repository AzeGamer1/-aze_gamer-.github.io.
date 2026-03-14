<?php
// Mesajların saklanacağı dosya
$filename = 'messages.json';

// Yeni mesaj gönderimini ele al
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $message = $_POST['message'];

    // Mevcut mesajları oku
    $messages = [];
    if (file_exists($filename)) {
        $json = file_get_contents($filename);
        $messages = json_decode($json, true);
    }

    // Yeni mesajı ekle
    $messages[] = [
        'user' => $user,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Mesajları dosyaya yaz
    file_put_contents($filename, json_encode($messages));

    exit();
}

// Mesajları getir
$messages = [];
if (file_exists($filename)) {
    $json = file_get_contents($filename);
    $messages = json_decode($json, true);
}

echo json_encode(array_reverse($messages));
?>