<?php
// Cihazdan gelen dosya ismini al (örneğin, POST yöntemi ile)
$filename = $_POST['filename'];

// Dosyanın var olup olmadığını kontrol et
if (file_exists($filename)) {
    // Dosyayı aç ve içeriğini oku
    $content = file_get_contents($filename);
    
    // İçeriği göster ve çık
    exit($content);
} else {
    // Dosya bulunamadıysa hata mesajı göster
    exit('Dosya bulunamadı.');
}
?>