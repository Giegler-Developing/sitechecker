<?php
/**
 * Created by PhpStorm.
 * User: Christian Giegler
 * Date: 08.07.2019
 * Time: 18:11
 */

$last_modified = filemtime("cms_versions.json");
$now = time();
$last_updated = date("d.m.Y H:i", filemtime("cms_versions.json"));

if ($now - $last_modified >= 3600 * 12) {
    include "functions.php";
    $cms_full_array = getCMSversions("all",FALSE);
    $json_data = json_encode($cms_full_array);
    file_put_contents('cms_versions.json', $json_data);
    $last_updated = date("d.m.Y H:i", time());
} else {
    $cms_full_array = json_decode(file_get_contents("cms_versions.json"), true);
}
ksort($cms_full_array);
$keys = array_keys($cms_full_array);

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
        echo "Last updated: $last_updated";
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
            foreach ($cms_full_array as $key => $val) {
                echo "<tr class=\"trow\"> <td class=\"columnbig\">$key</td><td class=\"columnsmall\">" . $val . "</td>\n";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>