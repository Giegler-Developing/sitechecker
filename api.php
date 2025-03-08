<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 10.07.2019
 * Time: 16:07
 */

include "functions.php";

// Extract repeated values into variables
$jsonFile = "cms_versions_single.json";
$lastModified = filemtime($jsonFile);
$currentTime = time();
$timeElapsed = $currentTime - $lastModified;

// Determine if update is needed
$forceUpdate = isset($_GET['force']) && $_GET['force'] === "true";
$dataExpired = $timeElapsed >= 3600 * 12;

// Fetch or update CMS data if necessary
$cmsData = fetchOrUpdateCMSData($dataExpired || $forceUpdate, $jsonFile);

// Set JSON content header
header("Content-Type: application/json");

// Handle API request
$requestedCMS = isset($_GET['cms']) ? $_GET['cms'] : null;
if ($requestedCMS === "list_all") {
    echo json_encode($cmsData);
} elseif ($requestedCMS && isset($cmsData[$requestedCMS])) {
    echo json_encode($cmsData[$requestedCMS]);
} else {
    echo json_encode(["empty" => "empty"]);
}

/**
 * Fetches CMS data or updates it if stale/forced.
 *
 * @param bool   $shouldUpdate Flag to specify whether data should be updated.
 * @param string $jsonFile     Path to the JSON file.
 *
 * @return array The CMS data array.
 */
function fetchOrUpdateCMSData(bool $shouldUpdate, string $jsonFile): array
{
    if ($shouldUpdate) {
        $cmsData = getCMSversions("all", true);
        file_put_contents($jsonFile, json_encode($cmsData));
    } else {
        $cmsData = json_decode(file_get_contents($jsonFile), true);
    }
    return $cmsData;
}