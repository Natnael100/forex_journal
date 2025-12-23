<?php

$logFile = 'storage/logs/laravel.log';

if (!file_exists($logFile)) {
    die("Log file not found.");
}

$f = fopen($logFile, 'r');
fseek($f, -4096, SEEK_END);
$content = fread($f, 4096);
fclose($f);

echo $content;
