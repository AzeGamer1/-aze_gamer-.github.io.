<?php
$dir = "audio/queue/";
if (!is_dir($dir)) mkdir($dir, 0777, true);

$file = $dir . time() . ".pcm";
file_put_contents($file, file_get_contents("php://input"));

echo "OK";
?>