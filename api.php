<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 10.07.2019
 * Time: 16:07
 */

include "functions.php";

$last_modified = filemtime("cms_versions_single.json");
$now = time();
$last_updated = date("d.m.Y H:i", filemtime("cms_versions_single.json"));

if ($now - $last_modified >= 3600 * 12 OR $_GET['force'] == "true") {

    $cms_full_array = getCMSversions("all",TRUE);
    $json_data = json_encode($cms_full_array);
    file_put_contents('cms_versions_single.json', $json_data);
    $last_updated = date("d.m.Y H:i", time());
} else {
    $cms_full_array = json_decode(file_get_contents("cms_versions_single.json"), true);
}

if(isset($_GET['cms']) and $_GET['cms'] != ""){
    $api_param = $_GET['cms'];
    if($api_param  == "list_all")
    {
        header("Content-Type: application/json");
        echo json_encode($cms_full_array);
    }
    else{
        header("Content-Type: application/json");
        echo json_encode($cms_full_array[$api_param]);
    }
}
else{
    header("Content-Type: application/json");
    echo json_encode( ["empty" => "empty"]);
}


