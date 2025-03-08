<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 08.07.2019
 * Time: 18:11
 */

// Constants
define('CACHE_EXPIRATION_SECONDS', 3600 * 12);
define('CMS_FILE_PATH', 'cms_versions.json');

/**
 * Get the last modified time of a file.
 */
function getFileLastModifiedTime($filePath): int
{
    return filemtime($filePath);
}

/**
 * Updates or retrieves CMS version data.
 */
function updateCMSVersions(bool $isForcedUpdate): array
{
    $lastModified = getFileLastModifiedTime(CMS_FILE_PATH);
    $currentTime = time();

    if ($isForcedUpdate || ($currentTime - $lastModified >= CACHE_EXPIRATION_SECONDS)) {
        include "functions.php";
        $cmsData = getCMSversions("all", false);
        $jsonData = json_encode($cmsData);
        file_put_contents(CMS_FILE_PATH, $jsonData);
    } else {
        $cmsData = json_decode(file_get_contents(CMS_FILE_PATH), true);
    }

    ksort($cmsData);
    return $cmsData;
}

// Main Logic
$isForcedUpdate = isset($_GET['force']) && $_GET['force'] === "true";
$cmsData = updateCMSVersions($isForcedUpdate);
$keys = array_keys($cmsData);
$lastUpdated = date("d.m.Y H:i", getFileLastModifiedTime(CMS_FILE_PATH));
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>CMS Version Information</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<div class="container">
    <div class="ctable">
        <?php
        echo "Last updated: $lastUpdated";
        ?>
        <table>
            <thead>
            <tr class="trow head">
                <th class="columnbig">CMS Name</th>
                <th class="columnsmall">Newest Version</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($cmsData as $key => $val) {
                echo "<tr class=\"trow\"> <td class=\"columnbig\">$key</td><td class=\"columnsmall\">" . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "</td>\n";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>