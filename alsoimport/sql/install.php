<?php
/**
* 2007-2015 PrestaShop
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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'alsoimport` (
    `id_alsoimport` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `also_cat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `presta_cat` int(11) NULL DEFAULT NULL,
    `brand` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    `stock` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 0,
    `margin1` decimal(5, 2) UNSIGNED NOT NULL,
    `margin2` decimal(5, 2) UNSIGNED NOT NULL,
    `margin3` decimal(5, 2) UNSIGNED NOT NULL,
    `margin4` decimal(5, 2) UNSIGNED NOT NULL,
    `margin5` decimal(5, 2) UNSIGNED NOT NULL,
    `margin6` decimal(5, 2) UNSIGNED NOT NULL,
    `margin7` decimal(5, 2) UNSIGNED NOT NULL,
    `margin8` decimal(5, 2) UNSIGNED NOT NULL,
    `margin9` decimal(5, 2) UNSIGNED NOT NULL,
    `margin10` decimal(5, 2) UNSIGNED NOT NULL,
    `margin11` decimal(5, 2) UNSIGNED NOT NULL,
    `margin12` decimal(5, 2) UNSIGNED NOT NULL,
    `margin13` decimal(5, 2) UNSIGNED NOT NULL,
    `margin14` decimal(5, 2) UNSIGNED NOT NULL,
    `margin15` decimal(5, 2) UNSIGNED NOT NULL,
    `position` int(11) UNSIGNED NOT NULL DEFAULT 0,
    `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `complete` tinyint(1) NULL DEFAULT 0,
    `cron_date` datetime(0) NOT NULL,
    `date_upd` datetime(0) NOT NULL,
    PRIMARY KEY (`id_alsoimport`),
    INDEX `id_alsoimport`(`id_alsoimport`) USING BTREE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'alsoimport_lang` (
    `id_alsoimport` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_lang` int(10) UNSIGNED NOT NULL,
    `also_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    PRIMARY KEY (`id_alsoimport`, `id_lang`) USING BTREE,
    INDEX `id_alsoimport`(`id_alsoimport`) USING BTREE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'alsoimport_shop` (
    `id_alsoimport` int(11) UNSIGNED NOT NULL,
    `id_shop` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_alsoimport`, `id_shop`) USING BTREE,
    INDEX `id_alsoimport`(`id_alsoimportas`) USING BTREE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'alsoimport_tree`  (
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `also_cat_id` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `also_cat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `group_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `also_cat_id`(`also_cat_id`) USING BTREE
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$skip = Db::getInstance()->getValue('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "'._DB_PREFIX_.'product" AND column_name = "skipname"');
if ($skip == 0) {
$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'product`
                    ADD COLUMN `skipname` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipstatus` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipdescr` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipimage` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipcat` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipstock` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipprice` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `skipseo` tinyint(1) NOT NULL DEFAULT 0,
                    ADD COLUMN `elkopid` varchar(255) NULL DEFAULT ""';
}

foreach ($sql as $query)
    if (Db::getInstance()->execute($query) == false)
        return false;
