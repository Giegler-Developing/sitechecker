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
    $fp = fopen($name, 'rb');

// send the right headers
    header("Content-Type: image/png");
    header("Content-Length: " . filesize($name));

// dump the picture and stop the script
    fpassthru($fp);
    exit;
} else {
    $name = '/var/www/sitecheck/screen.jpg';
    $fp = fopen($name, 'rb');

// send the right headers
    header("Content-Type: image/png");
    header("Content-Length: " . filesize($name));

// dump the picture and stop the script
    fpassthru($fp);
    exit;
} ?>