<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 10.07.2019
 * Time: 14:36
 */

$last_modified = filemtime("screen.jpg");
$now = time();

if ($now - $last_modified >= 3600 * 12) {
    $output = shell_exec('wkhtmltoimage https://sitecheck.giegler.software/ /var/www/sitecheck/screen.jpg');
    $name = '/var/www/sitecheck/screen.jpg';
    $version_image_file = fopen($name, 'rb');

    header("Content-Type: image/png");
    header("Content-Length: " . filesize($name));

    fpassthru($version_image_file);
    exit;
} else {
    $name = '/var/www/sitecheck/screen.jpg';
    $version_image_file = fopen($name, 'rb');

    header("Content-Type: image/png");
    header("Content-Length: " . filesize($name));

    fpassthru($version_image_file);
    exit;
} ?>