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

function getVersionsViaGithub($github_url,$cms_name,$version_length,$special_chars =""){
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
    $cms_versions_final = [];
    for ($i = 0; $i < count($cms_version_array_clear); $i++) {
        $version_check = checkVersionExists($cms_versions_final,$cms_version_array_clear[$i],$version_length);
        if($version_check != 9999)
        {
            if(version_compare($cms_version_array_clear[$i],$cms_versions_final[$version_check],'>=')){
                $cms_versions_final[$version_check] = $cms_version_array_clear[$i];
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

function getCMSversions()
{
    global $cms_array;

// Get Newest Wordpress Version

    $wp_versions = getVersionsViaGithub("https://github.com/WordPress/WordPress/releases","Wordpress", 1);

// Get Newest Joomla Version

    $joomla_versions = getVersionsViaGithub("https://github.com/joomla/joomla-cms/releases","Joomla", 1);

// Get Newest Drupal Versions

    $drupal_versions = getVersionsViaGithub("https://github.com/drupal/drupal/releases","Drupal", 1);

// Get Newest Typo3 Versions

    $typo3_versions = getVersionsViaGithub("https://github.com/TYPO3/TYPO3.CMS/releases","Typo3", 1,"v");

// Get Shopware Version

    $shopware_version = getVersionsViaGithub("https://github.com/shopware/shopware/releases","Shopware",1);

// Get Magento 2 Version

    $magento2_version = getVersionsViaGithub("https://github.com/magento/magento2/releases","Magento",1,"\/");

// Get Woocommerce Version

    $woocommerce_version = getVersionsViaGithub("https://github.com/woocommerce/woocommerce/releases","Woocommerce",1);

// Get Nextcloud Version

    $nextcloud_versions = getVersionsViaGithub("https://github.com/nextcloud/server/releases","Nextcloud",2);

// Get Owncloud Version

    $owncloud_versions = getVersionsViaGithub("https://github.com/owncloud/core/releases","Owncloud",2);

// Get Matomo/Piwik Version

    $matomo_versions = getVersionsViaGithub("https://github.com/matomo-org/matomo/releases","Matomo/Piwik",1);

// Get Moodle Version

    $moodle_versions = getVersionsViaGithub("https://github.com/moodle/moodle/releases","Moodle",1,"v");

// Get OXID Version

    $oxid_versions = getVersionsViaGithub("https://github.com/OXID-eSales/oxideshop_ce/releases","OXID Shop",1,"v");

// Get Contao 3 Version

    $contao3_versions = getVersionsViaGithub("https://github.com/contao/core/releases","Contao",1);

// Get Contao 4 Version

    $contao4_versions = getVersionsViaGithub("https://github.com/contao/contao/releases","Contao",1);

// Get PrestaShop Version

    $prestashop_versions = getVersionsViaGithub("https://github.com/PrestaShop/PrestaShop/releases","PrestaShop",1);

// Get Gambio Version

    $gambio_header = shell_exec("curl -L --head  https://www.gambio.de/shortify.php?s=3eUC6");
    preg_match('/filename="Gambio v(.*)\.zip"/',$gambio_header, $matches);
    $version_name = "Gambio ". substr($matches[1], 0, 1);
    $cms_array[$version_name] .= $matches[1];

    return $cms_array;
}
?>
