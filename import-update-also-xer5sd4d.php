<?php
ini_set('memory_limit', '1G');
ini_set("gd.jpeg_ignore_warning", 1);
ini_set('max_execution_time', 0);


require_once(__DIR__.'/config/defines.inc.php');
require_once(__DIR__.'/config/config.inc.php');
require_once(__DIR__.'/classes/PrestaShopAutoload.php');
require_once(__DIR__.'/init.php');

set_time_limit(0);

//import-update-also-xer5sd4d.php?token=rYPzaJvN
//$linktoken = Tools::getValue("also1589e6d132asd");
$linktoken = "also1589e6d132asd";
//$updateProduct = Tools::getValue('type');
if ($linktoken != Tools::getValue('token')) {
 print("Token error...");
 die();
}

$time_start = microtime(true);
$sql = '
        SELECT *
        FROM `' . _DB_PREFIX_ . 'alsoimportas`
        WHERE `active` = "1"
        ORDER BY `id_alsoimportas` ASC';

$_also_filters = Db::getInstance()->executeS($sql);

$delay = rand(2,32);
sleep(15);

$isSecure = false;
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $isSecure = true;
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https://'.Configuration::get('PS_SHOP_DOMAIN_SSL') : 'http://'.Configuration::get('PS_SHOP_DOMAIN_SSL');

$ch = curl_init();

foreach ($_also_filters as $key=>$_also_filter) {
curl_setopt($ch, CURLOPT_URL, $REQUEST_PROTOCOL."/modules/alsoimportas/AlsoImportUpdate.php?token=also1589e6d132asd&updateId=" . $_also_filter['id_alsoimportas']);
curl_setopt($ch, CURLOPT_HEADER, 0);

// grab URL and pass it to the browser
curl_exec($ch);
//sleep(5);
}
// close cURL resource, and free up system resources
curl_close($ch);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start)/60;

//execution time of the script
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
/*
echo "<pre>";
print_r($_also_filter);
*/