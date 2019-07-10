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

function getVersionsViaGithub($github_url,$cms_name,$version_length,$special_chars ="",$single_version=FALSE){
    global $cms_array;
    $cms_version_list = shell_exec("curl -SsL $github_url | awk '/\/tag\//'");
    $cms_version_array =  preg_split('/[\r\n]+/', $cms_version_list);
    $cms_version_array_clear = [];
    for ($i = 0; $i < count($cms_version_array); $i++) {
        preg_match('/'.$special_chars.'([0-9.]+).*">/',$cms_version_array[$i], $matches);
        preg_match('/'.$special_chars.'([0-9]+.*)">/',$cms_version_array[$i], $matches2);
        if($matches2[1] == $matches[1]) {
            $cms_version_array_clear[$i] = $matches[1];
        }
    }
    if($single_version == TRUE)
    {
        $cms_name = strtolower($cms_name);
        for ($i = 0; $i < count($cms_version_array_clear); $i++) {
            if(!array_key_exists($cms_name,$cms_array))
            {
                $cms_array["$cms_name"] = $cms_version_array_clear[$i];
            }
            else{
                if (version_compare($cms_version_array_clear[$i], $cms_array[$cms_name], '>=')) {
                    $cms_array["$cms_name"] = $cms_version_array_clear[$i];
                }
            }
        }
        return $cms_array;
        exit;
    }

    $cms_versions_final = [];
    for ($i = 0; $i < count($cms_version_array_clear); $i++) {
        $version_check = checkVersionExists($cms_versions_final,$cms_version_array_clear[$i],$version_length);
        if($version_check != 9999)
        {
            if(version_compare($cms_version_array_clear[$i],$cms_versions_final[$version_check],'>=')){
                $cms_versions_final["$version_check"] = $cms_version_array_clear[$i];
            }
        }
        elseif($version_check == 9999)
        {
            $cms_versions_final[] = $cms_version_array_clear[$i];
        }
    }
    $cms_versions_final = array_values(array_filter($cms_versions_final));
    $cms_versions_final_return_code = "";
    for ($i = 0; $i < count($cms_versions_final); $i++) {
        $version_name = $cms_name." ". substr($cms_versions_final[$i], 0, $version_length);
        $cms_array["$version_name"] .= $cms_versions_final[$i];
        $cms_versions_final_return_code .= "<tr><td>$cms_name ". substr($cms_versions_final[$i], 0, $version_length) ."</td><td>$cms_versions_final[$i]</td></tr>";
    }
    return $cms_versions_final_return_code;
}

function getCMSversions($requested_cms,$single_version)
{
    global $cms_array;
    
// Get Newest Wordpress Version

        $cms['wordpress'] = getVersionsViaGithub("https://github.com/WordPress/WordPress/releases", "Wordpress", 1,"",$single_version);

// Get Newest Joomla Version

        $cms['joomla'] = getVersionsViaGithub("https://github.com/joomla/joomla-cms/releases", "Joomla", 1,"",$single_version);

// Get Newest Drupal Versions

        $cms['drupal'] = getVersionsViaGithub("https://github.com/drupal/drupal/releases", "Drupal", 1,"",$single_version);

// Get Newest Typo3 Versions

        $cms['typo3'] = getVersionsViaGithub("https://github.com/TYPO3/TYPO3.CMS/releases", "Typo3", 1, "v",$single_version);

// Get Shopware Version

        $cms['shopware'] = getVersionsViaGithub("https://github.com/shopware/shopware/releases", "Shopware", 1,"",$single_version);

// Get Magento 2 Version

        $cms['magento2'] = getVersionsViaGithub("https://github.com/magento/magento2/releases", "Magento", 1, "\/",$single_version);

// Get Woocommerce Version

        $cms['woocommerce'] = getVersionsViaGithub("https://github.com/woocommerce/woocommerce/releases", "Woocommerce", 1,"",$single_version);

// Get Nextcloud Version

        $cms['nextcloud'] = getVersionsViaGithub("https://github.com/nextcloud/server/releases", "Nextcloud", 2,"",$single_version);

// Get Owncloud Version

        $cms['owncloud'] = getVersionsViaGithub("https://github.com/owncloud/core/releases", "Owncloud", 2,"",$single_version);

// Get Matomo/Piwik Version

        $cms['matomo'] = getVersionsViaGithub("https://github.com/matomo-org/matomo/releases", "Matomo", 1,"",$single_version);

// Get Moodle Version

        $cms['moodle'] = getVersionsViaGithub("https://github.com/moodle/moodle/releases", "Moodle", 1, "v",$single_version);

// Get OXID Version

        $cms['oxid'] = getVersionsViaGithub("https://github.com/OXID-eSales/oxideshop_ce/releases", "OXID", 1, "v",$single_version);

// Get Contao 3 Version

        $cms['contao3'] = getVersionsViaGithub("https://github.com/contao/core/releases", "Contao", 1,"",$single_version);

// Get Contao 4 Version

        $cms['contao4'] = getVersionsViaGithub("https://github.com/contao/contao/releases", "Contao", 1,"",$single_version);

// Get PrestaShop Version

        $cms['prestashop'] = getVersionsViaGithub("https://github.com/PrestaShop/PrestaShop/releases", "Prestashop", 1,"",$single_version);

// Get Gambio Version

        $gambio_header = shell_exec("curl -L --head  https://www.gambio.de/shortify.php?s=3eUC6");
        preg_match('/filename="Gambio v(.*)\.zip"/', $gambio_header, $matches);
        $cms_array["Gambio"] .= $matches[1];

    if($requested_cms == "all") {
        return $cms_array;
    }
    elseif($requested_cms != "all" && $requested_cms != "") {
     return $cms["$requested_cms"];
    }
}
?>
