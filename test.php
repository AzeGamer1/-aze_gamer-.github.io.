<?php

$directory = './'; // Dosyaların bulunduğu dizin

$files = array_diff(scandir($directory), array('.', '..')); // '.' ve '..' hariç tüm dosyaları al

echo json_encode($files, JSON_PRETTY_PRINT); // Dosya isimlerini JSON formatında göster

?>