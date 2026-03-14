<?php

$loginDataFile = 'LoginData.data';

if (file_exists($loginDataFile)) {

    $file = fopen($loginDataFile, 'r');

    $loginData = fread($file, filesize($loginDataFile));

    fclose($file);

    $users = json_decode($loginData, true);

    $username = $_POST['username'] ?? '';

    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && $users[$username]['password'] === $password) {

        exit("azs=91");

    }

}

exit("azs=0");

?>