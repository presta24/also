<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once('/config/defines.inc.php');
require_once('/config/config.inc.php');
require_once('/classes/PrestaShopAutoload.php');
require_once('/init.php');
//echo Tools::substr(_COOKIE_KEY_, 34, 8);
/*if (Tools::substr(_COOKIE_KEY_, 34, 8) != Tools::getValue('token')){
    die();
}*/
//echo "OK";
$dateUpdate = Db::getInstance()->executeS('SELECT `id_product`,`date_upd`,`skipstatus`,`active` FROM `'._DB_PREFIX_.'product` WHERE `alsopid` LIKE "AC_%"');


if ($dateUpdate) {
	foreach ($dateUpdate as $row) {
	
		if($row['date_upd'] <= date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) AND $row['skipstatus']==0 AND $row['active']=='0') {
			$pDel = new Product($row['id_product']);
				if(!$pDel->delete()) {
					echo " <span style='color: red'>Error deleting this product!</span></p>";
				} else {
					echo $row['id_product']." <span style='color: green'>DELETED</span></p>";
				}
		}

if($row['date_upd'] <= date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-3, date("Y"))) AND $row['skipstatus']==0 AND $row['active']=='1'){
  		$pSave = new Product($row['id_product']);
			$pSave->active              = 0;
			$pSave->price              = 0;
			$pSave->available_for_order = 0;
			$pSave->visibility          = 'none';
		if(!$pSave->save()) {
				echo " <span style='color: red'>Error UPDATE this product!</span></p>";
			} else {
				echo $row['id_product']." <span style='color: green'>Product OFF</span></p>";
			}

	}

}
 }