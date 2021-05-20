<?php

include_once dirname(__FILE__) .'/config/config.inc.php';
include_once dirname(__FILE__) .'/config/settings.inc.php';
include_once dirname(__FILE__) .'/config/defines.inc.php';
include_once dirname(__FILE__) .'/init.php';

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');

define('ALSOIMPORT_CLIENTID', Configuration::get('ALSOIMPORT_CLIENTID'));
define('ALSOIMPORT_USERNAME', Configuration::get('ALSOIMPORT_USERNAME'));
define('ALSOIMPORT_PASSWORD', Configuration::get('ALSOIMPORT_PASSWORD'));

function getAlsoInsertTree($also_cat_id, $also_cat,$group_id)
{
 //Also fill tree in prestashop
 $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'alsoimport_tree` (`also_cat_id`, `also_cat`,`group_id`) VALUES ("'.pSQL($also_cat_id).'","'.pSQL($also_cat).'","'.pSQL($group_id).'")';
 return Db::getInstance()->execute($sql);

}

$truncate = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'alsoimport_tree`';
echo Db::getInstance()->execute($truncate);

$xml = simplexml_load_file('http://directxml.also.lt/DirectXML.svc/GetGrouping/3/'.ALSOIMPORT_CLIENTID) or die("Error: Cannot create object");
$xml->xpath('//GroupBy[@GroupID="ClassID"');
foreach($xml as $key=>$group) {
/*	if($group['GroupID']=='VendorID')
	continue;*/
	//echo $key." - ".$group['Value']." - ".$group['Description']."<br>";
	getAlsoInsertTree($group['Value'],$group['Description'],$group['GroupID']);

}


echo "<pre>";
//print_r($xml);
echo "</pre>";