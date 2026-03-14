<?php
if(isset($_POST['files'])){
    $files = json_decode($_POST['files'], true);
    if(!is_array($files)) $files = array();

    // fayl siyahısını JSON faylda saxla
    file_put_contents("file_list.json", json_encode($files, JSON_PRETTY_PRINT));

    echo "OK";
    exit;
}
?>