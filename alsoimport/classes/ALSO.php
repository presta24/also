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

class Also extends ObjectModel
{
    /**
     * GET config value
     */
    protected $position_identifier = 'id_alsoimport';

    /** @var string Name */
    public $id_alsoimport;
   //public $also_name;
    public $also_cat;
    public $presta_cat;
    public $brand;
   // public $byorder;
    public $margin1;
    public $margin2;
    public $margin3;
    public $margin4;
    public $margin5;
    public $margin6;
    public $margin7;
    public $margin8;
    public $margin9;
    public $margin10;
    public $margin11;
    public $margin12;
    public $margin13;
    public $margin14;
    public $margin15;
    public $position;
    public $info;
    public $active;
    public $stock;
    public $complete;
    public $cron_date;
    public $date_upd;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'alsoimport',
        'primary' => 'id_alsoimport',
        'multilang' => FALSE,
        'fields' => array(
            'also_cat'      => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE),
            'presta_cat'    => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE,'size' => 255),
            'brand'         => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => FALSE,'size' => 255),
            //'byorder'       => array('type' => self::TYPE_BOOL),
            'margin1'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin2'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin3'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin4'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin5'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin6'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin7'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin8'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin9'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin10'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin11'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin12'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin13'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin14'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'margin15'       => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'position'      => array('type' => self::TYPE_INT),
            'active'        => array('type' => self::TYPE_BOOL),
            'stock'     => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => TRUE),
            'complete'      => array('type' => self::TYPE_BOOL),
            'cron_date'     => array('type' => self::TYPE_DATE),
            'date_upd'      => array('type' => self::TYPE_DATE),

            // Lang fields
           // 'also_name'     => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'info'          => array('type' => self::TYPE_HTML, 'lang' => false, 'validate' => 'isString', 'size' => 3999999999999),
        ),
    );



    public function add($autodate = TRUE, $null_values = FALSE)
    {
        $this->position = self::getLastPosition();
        return parent::add($autodate, TRUE);
    }

    public function update($null_values = FALSE)
    {
        if (parent::update($null_values)) {
            return $this->cleanPositions();
        }

        return FALSE;
    }

    public function delete()
    {
        if (parent::delete()) {
            return $this->cleanPositions();
        }

        return FALSE;
    }

    public static function getLastPosition()
    {
        $sql = '
        SELECT MAX(position) + 1
        FROM `'._DB_PREFIX_.'alsoimport`';

        return (Db::getInstance()->getValue($sql));
    }

    public static function cleanPositions()
    {
        $sql = '
        SELECT `id_alsoimport`
        FROM `'._DB_PREFIX_.'alsoimport`
        ORDER BY `position`';

        $result = Db::getInstance()->executeS($sql);

        for ($i = 0, $total = count($result); $i < $total; ++$i) {
            $sql = 'UPDATE `'._DB_PREFIX_.'alsoimport`
                    SET `position` = '.(int)$i.'
                    WHERE `id_alsoimport` = '.(int)$result[$i]['id_alsoimport'];
            Db::getInstance()->execute($sql);
        }

        return TRUE;
    }

    public function updatePosition($way, $position)
    {
        if (!$res = Db::getInstance()->executeS('
            SELECT cp.`id_alsoimport`, cp.`position`
            FROM `'._DB_PREFIX_.'alsoimport` cp
            ORDER BY cp.`position` ASC'
        )) {
            return FALSE;
        }

        foreach ($res as $filter) {
            if ((int)$filter['id_alsoimport'] == (int)$this->id) {
                $moved_filter = $filter;
            }
        }

        if (!isset($moved_filter) || !isset($position)) {
            return FALSE;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'alsoimport`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
                ? '> '.(int)$moved_filter['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_filter['position'].' AND `position` >= '.(int)$position))
        && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'alsoimport`
            SET `position` = '.(int)$position.'
            WHERE `id_alsoimport` = '.(int)$moved_filter['id_alsoimport']));
    }

    /**
    ** GEt also category code from filter
    */
    public static function getFilterCat()
    {
        $sql = '
        SELECT `also_cat`
        FROM `'._DB_PREFIX_.'alsoimport`
        WHERE `id_alsoimport` = 1';

        return Db::getInstance()->getValue($sql);
    }

    /**
    ** GEt also vendor code from filter
    */
    public static function getFilterBrand()
    {
        $sql = '
        SELECT `brand`
        FROM `'._DB_PREFIX_.'alsoimport`
        WHERE `id_alsoimport` = 1';

        return Db::getInstance()->getValue($sql);
    }

    /**
    ** GEt also category list
    */
    public static function getAlsoCategory()
    {

        $sql = '
        SELECT also_cat_id, also_cat
        FROM `'._DB_PREFIX_.'alsoimport_tree`
        WHERE group_id = "ClassID"
        ORDER BY `also_cat` ASC';

        $categories = Db::getInstance()->executeS($sql);

        $alsoCat = array();
        foreach ($categories as $cat)
            {
                $alsoCat[] = array(
                  "value" => $cat['also_cat_id'],
                  "name"  => $cat['also_cat']
                );
            }
        return $alsoCat;
        //return (Db::getInstance()->getValue($sql));
    }
    /**
    ** GEt also vendor list
    */
    public static function getAlsoVendor()
    {

        $sql = '
        SELECT also_cat_id, also_cat
        FROM `' . _DB_PREFIX_ . 'alsoimport_tree`
        WHERE group_id = "VendorID"
        ORDER BY also_cat ASC';

        $alsoBrands = Db::getInstance()->executeS($sql);
        $alsoVendor = array();
        $i=0;
        foreach ($alsoBrands as $brand)
            {
                 $alsoVendor[] = array(
                    "id_brand" => $brand['also_cat_id'],
                    "brands"  => $brand['also_cat']
                );
           // $i++;
            }
        return $alsoVendor;

    }
    /**
    ** GEt also vendor list in grid
    */
    public static function getAlsoVendorList()
    {

        $sql = '
        SELECT brand
        FROM `' . _DB_PREFIX_ . 'alsoimport_brand`
        ';

        $alsoBrand = Db::getInstance()->getValue($sql);

        return $alsoBrand;
        //return (Db::getInstance()->getValue($sql));
    }
    /**
    ** GEt also vendor list in grid
    */
    public static function getAlsoTreeList()
    {

        $sql = '
        SELECT name
        FROM `' . _DB_PREFIX_ . 'alsoimport_tree`
        ';

        $alsoTree = Db::getInstance()->getValue($sql);

        return $alsoTree;
        //return (Db::getInstance()->getValue($sql));
    }




}
