<?php
$JDecode = json_decode(file_get_contents('php://input'),true); 




$file = $JDecode["filename"];

// Dosyanın mevcut olup olmadığını kontrol et

if (file_exists($file)) {

    // Dosyayı sil

    unlink($file);

    echo "Dosya başarıyla silindi.";

} else {

    echo "Dosya bulunamadı.";

}

?>