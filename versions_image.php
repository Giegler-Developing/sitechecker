<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 10.07.2019
 * Time: 14:36
 */

const CACHE_EXPIRATION_TIME = 3600 * 3;

$isForceUpdateRequested = isset($_GET['force']) && $_GET['force'] === 'true';
$file = __DIR__ . "/screen.jpg";
$lastModified = filemtime($file);
$now = time();

// Check if the image needs to be updated
if ($now - $lastModified >= CACHE_EXPIRATION_TIME || $isForceUpdateRequested) {
    $url = "https://" . $_SERVER['SERVER_NAME'];
    shell_exec("/usr/local/bin/wkhtmltoimage $url $file");
}

// Serve the image file
serveImageFile($file);

function serveImageFile(string $filePath): void {
    $imageFile = fopen($filePath, 'rb');
    header("Content-Type: image/jpeg");
    header("Content-Length: " . filesize($filePath));
    fpassthru($imageFile);
    exit;
}
