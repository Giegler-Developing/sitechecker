<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 05.07.2019
 * Time: 14:23
 */

$cms_array = [];

function checkVersionExists($array_versions,$value_to_check,$version_length){
    $version_check_major = substr($value_to_check, 0, $version_length);
    for ($i = 0; $i < count($array_versions); $i++) {
        if(substr($array_versions[$i], 0, $version_length) == $version_check_major){
            return $i;
        }
    }
    return 9999;
}

function getVersionsViaGithub($github_url,$cms_name,$version_length,$single_version=FALSE, $useTags = FALSE){
    global $cms_array;

    if(isset($_SERVER["client_id"]) AND isset($_SERVER["client_secret"])) {
        $final_url = $github_url . "?client_id=" . $_SERVER["client_id"] . "&client_secret=" . $_SERVER["client_secret"];
    }
    else{
        $final_url = $github_url;
    }
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_URL             => $final_url,
        CURLOPT_USERAGENT       => 'CMS-Check 1.0 (https://gitlab.com/gidev/sitechecker)',
        CURLOPT_CONNECTTIMEOUT  => 2,
        CURLOPT_TIMEOUT         => 5
    ));
    $releases = curl_exec($ch);
    curl_close($ch);
    $releases_array = json_decode($releases,true);
    for ($i = 0; $i < count($releases_array); $i++) {
        if($useTags == FALSE) {
            preg_match('/([0-9.]+).*/', $releases_array[$i]["tag_name"], $matches);
        }
        elseif($useTags == TRUE){
            preg_match('/([0-9.]+).*/', $releases_array[$i]["name"], $matches);
        }
        if($matches[0] == $matches[1]) {
            $cms_array_final[] = $matches[0];
        }
    }

    if($single_version == TRUE)
    {
        $cms_name = strtolower($cms_name);
        for ($i = 0; $i < count($cms_array_final); $i++) {
            if(!array_key_exists($cms_name,$cms_array))
            {
                $cms_array["$cms_name"] = $cms_array_final[$i];
            }
            else{
                if (version_compare($cms_array_final[$i], $cms_array[$cms_name], '>=')) {
                    $cms_array["$cms_name"] = $cms_array_final[$i];
                }
            }
        }
        return $cms_array;
    }
    elseif($single_version == FALSE) {
        $cms_versions_final = [];
        for ($i = 0; $i < count($cms_array_final); $i++) {
            $version_check = checkVersionExists($cms_versions_final, $cms_array_final[$i], $version_length);
            if ($version_check != 9999) {
                if (version_compare($cms_array_final[$i], $cms_versions_final[$version_check], '>=')) {
                    $cms_versions_final["$version_check"] = $cms_array_final[$i];
                }
            } elseif ($version_check == 9999) {
                $cms_versions_final[] = $cms_array_final[$i];
            }
        }
        $cms_versions_final = array_values(array_filter($cms_versions_final));
        $cms_versions_final_return_code = "";
        for ($i = 0; $i < count($cms_versions_final); $i++) {
            $version_name = $cms_name . " " . substr($cms_versions_final[$i], 0, $version_length);
            $cms_array["$version_name"] .= $cms_versions_final[$i];
            $cms_versions_final_return_code .= "<tr><td>$cms_name " . substr($cms_versions_final[$i], 0, $version_length) . "</td><td>$cms_versions_final[$i]</td></tr>";
        }
        return $cms_versions_final_return_code;
    }
}

function getCMSversions($requested_cms,$single_version)
{
    global $cms_array;

// Get Newest Wordpress Version

    $cms['wordpress'] = getVersionsViaGithub("https://api.github.com/repos/WordPress/WordPress/tags", "Wordpress", 1,$single_version, true);

// Get Newest Joomla Version

    $cms['joomla'] = getVersionsViaGithub("https://api.github.com/repos/joomla/joomla-cms/releases", "Joomla", 1,$single_version);

// Get Newest Drupal Versions

    $cms['drupal'] = getVersionsViaGithub("https://api.github.com/repos/drupal/drupal/tags", "Drupal", 1,$single_version, true);

// Get Newest Typo3 Versions

    $cms['typo3'] = getVersionsViaGithub("https://api.github.com/repos/TYPO3/TYPO3.CMS/tags", "Typo3", 1, $single_version, true);

// Get Shopware Version

    $cms['shopware'] = getVersionsViaGithub("https://api.github.com/repos/shopware/shopware/releases", "Shopware", 1,$single_version);

// Get Magento 2 Version

    $cms['magento2'] = getVersionsViaGithub("https://api.github.com/repos/magento/magento2/releases", "Magento", 1, $single_version);

// Get Woocommerce Version

    $cms['woocommerce'] = getVersionsViaGithub("https://api.github.com/repos/woocommerce/woocommerce/releases", "Woocommerce", 1,$single_version);

// Get Nextcloud Version

    $cms['nextcloud'] = getVersionsViaGithub("https://api.github.com/repos/nextcloud/server/releases", "Nextcloud", 2,$single_version);

// Get Owncloud Version

    $cms['owncloud'] = getVersionsViaGithub("https://api.github.com/repos/owncloud/core/tags", "Owncloud", 2,$single_version, true);

// Get Matomo/Piwik Version

    $cms['matomo'] = getVersionsViaGithub("https://api.github.com/repos/matomo-org/matomo/releases", "Matomo", 1,$single_version);

// Get Moodle Version

    $cms['moodle'] = getVersionsViaGithub("https://api.github.com/repos/moodle/moodle/tags", "Moodle", 1, $single_version, true);

// Get OXID Version

    $cms['oxid'] = getVersionsViaGithub("https://api.github.com/repos/OXID-eSales/oxideshop_ce/tags", "OXID", 1, $single_version, true);

// Get Contao 3 Version

    $cms['contao3'] = getVersionsViaGithub("https://api.github.com/repos/contao/core/releases", "Contao", 1,$single_version);

// Get Contao 4 Version

    $cms['contao4'] = getVersionsViaGithub("https://api.github.com/repos/contao/contao/tags", "Contao", 1,$single_version, true);

// Get PrestaShop Version

    $cms['prestashop'] = getVersionsViaGithub("https://api.github.com/repos/PrestaShop/PrestaShop/releases", "Prestashop", 1,$single_version);

// Get Gambio Version

    $gambio_header = shell_exec("curl -L --head  https://www.gambio.de/shortify.php?s=3eUC6");
    preg_match('/filename="Gambio v(.*)\.zip"/', $gambio_header, $matches);
    $cms_array["gambio"] .= $matches[1];

    if($requested_cms == "all") {
        return $cms_array;
    }
    elseif($requested_cms != "all" && $requested_cms != "") {
        return $cms["$requested_cms"];
    }

}
?>