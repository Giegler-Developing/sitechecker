<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 05.07.2019
 * Time: 14:23
 */

// Load .env file and store settings in constants.
$env = parse_ini_file(__DIR__ . '/.env');
define('GITHUB_USER_AGENT', $env['GITHUB_USER_AGENT'] ?? 'CMS-Check 1.0 (https://github.com/Giegler-Developing/sitechecker)');
define('GITHUB_USER', $env['GITHUB_USER'] ?? '');
define('GITHUB_TOKEN', $env['GITHUB_TOKEN'] ?? '');
define('GITHUB_TIMEOUT', $env['GITHUB_TIMEOUT'] ?? 5);

/**
 * Fetches releases or tags from the GitHub API.
 */
function fetchGithubReleases(string $url): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_URL             => $url,
        CURLOPT_USERPWD         => GITHUB_USER . ':' . GITHUB_TOKEN,
        CURLOPT_USERAGENT       => GITHUB_USER_AGENT,
        CURLOPT_CONNECTTIMEOUT  => 2,
        CURLOPT_TIMEOUT         => GITHUB_TIMEOUT
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? [];
}

/**
 * Checks if a version already exists in the list based on the major version prefix.
 */
function checkVersionExists(array $versions, string $checkVersion, int $versionLength): ?int {
    $majorVersionCheck = substr($checkVersion, 0, $versionLength);
    foreach ($versions as $index => $version) {
        if (substr($version, 0, $versionLength) === $majorVersionCheck) {
            return $index;
        }
    }
    return null;
}

/**
 * Updates the CMS array with the highest version or all versions.
 */
function updateCmsArray(string $cmsName, array $versions, array $cmsArray, bool $singleVersion, int $versionLength): mixed {
    $finalCmsVersions = [];

    foreach ($versions as $newVersion) {
        $existingIndex = checkVersionExists($finalCmsVersions, $newVersion, $versionLength);
        if ($existingIndex !== null) {
            if (version_compare($newVersion, $finalCmsVersions[$existingIndex], '>=')) {
                $finalCmsVersions[$existingIndex] = $newVersion;
            }
        } else {
            $finalCmsVersions[] = $newVersion;
        }
    }

    if ($singleVersion) {
        $cmsNameKey = strtolower($cmsName);
        foreach ($finalCmsVersions as $version) {
            if (!isset($cmsArray[$cmsNameKey]) || version_compare($version, $cmsArray[$cmsNameKey], '>=')) {
                $cmsArray[$cmsNameKey] = $version;
            }
        }
        return $cmsArray[$cmsNameKey];
    }

    $resultHtml = '';
    foreach ($finalCmsVersions as $version) {
        $versionDisplayName = $cmsName . ' ' . substr($version, 0, $versionLength);
        $cmsArray[$versionDisplayName] = $version;
    }
    return $cmsArray;
}

/**
 * Retrieves all CMS versions via GitHub API.
 */
function getVersionsViaGithub(
    string $githubUrl,
    string $cmsName,
    int $versionLength,
    bool $singleVersion = false,
    bool $useTags = false,
    array &$cmsArray = []
): mixed {
    $releases = fetchGithubReleases($githubUrl);
    $filteredVersions = [];

    foreach ($releases as $release) {
        $tagField = $useTags ? 'name' : 'tag_name';
        if (isset($release[$tagField])) {
            preg_match('/([0-9.]+).*/', $release[$tagField], $matches);
            if (!empty($matches) && $matches[0] === $matches[1]) {
                $filteredVersions[] = $matches[0];
            }
        }
    }

    return updateCmsArray($cmsName, $filteredVersions, $cmsArray, $singleVersion, $versionLength);
}

/**
 * Main function to fetch and return CMS versions.
 */
function getCMSversions(string $requestedCms, bool $singleVersion): mixed {
    $cmsList = [
        'wordpress'  => ["https://api.github.com/repos/WordPress/WordPress/tags", "Wordpress", 1, true],
        'joomla'     => ["https://api.github.com/repos/joomla/joomla-cms/releases", "Joomla", 1],
        'drupal'     => ["https://api.github.com/repos/drupal/drupal/tags", "Drupal", 2, true],
        'typo3'      => ["https://api.github.com/repos/TYPO3/typo3/tags", "Typo3", 2, true],
        'shopware'   => ["https://api.github.com/repos/shopware/shopware/releases", "Shopware", 1],
        'magento2'   => ["https://api.github.com/repos/magento/magento2/releases", "Magento", 1],
        'woocommerce'=> ["https://api.github.com/repos/woocommerce/woocommerce/releases", "Woocommerce", 1],
        'nextcloud'  => ["https://api.github.com/repos/nextcloud/server/tags", "Nextcloud", 2, true],
        'owncloud'   => ["https://api.github.com/repos/owncloud/core/tags", "Owncloud", 2, true],
        'matomo'     => ["https://api.github.com/repos/matomo-org/matomo/releases", "Matomo", 1],
        'moodle'     => ["https://api.github.com/repos/moodle/moodle/tags", "Moodle", 1, true],
        'oxid'       => ["https://api.github.com/repos/OXID-eSales/oxideshop_ce/tags", "OXID", 1, true],
        'contao3'    => ["https://api.github.com/repos/contao/core/releases", "Contao", 1],
        'contao4'    => ["https://api.github.com/repos/contao/contao/tags", "Contao", 1, true],
        'prestashop' => ["https://api.github.com/repos/PrestaShop/PrestaShop/releases", "Prestashop", 1],
    ];

    $cmsArray = [];

    foreach ($cmsList as $key => [$url, $name, $versionLength, $tagsUsed]) {
        $cmsArray = getVersionsViaGithub($url, $name, $versionLength, $singleVersion, $tagsUsed ?? false, $cmsArray);
    }

    if ($requestedCms === "all") {
        return $cmsArray;
    }

    return $cmsArray[$requestedCms] ?? null;
}

?>
