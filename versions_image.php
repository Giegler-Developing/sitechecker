<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 10.07.2019
 * Time: 14:36
 */

$force = FALSE;
if(isset($_GET['force'])){
$force = $_GET['force'];
}
$file = __DIR__."/screen.jpg";

$last_modified = filemtime($file);
$now = time();

if ($now - $last_modified >= 3600 * 3 OR $force == "true") {
	$url = "https://".$_SERVER['SERVER_NAME'];
    $output = shell_exec("/usr/local/bin/wkhtmltoimage $url $file");
    $version_image_file = fopen($file, 'rb');

    header("Content-Type: image/png");
    header("Content-Length: " . filesize($file));

    fpassthru($version_image_file);
    exit;
} else {
    $version_image_file = fopen($file, 'rb');

    header("Content-Type: image/png");
    header("Content-Length: " . filesize($file));

    fpassthru($version_image_file);
    exit;
} ?>
