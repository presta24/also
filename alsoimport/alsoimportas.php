<?php
/*
 * @author    Presta24.eu <info@presta24.eu>
 * @site
 * @copyright  Copyright (c) 2021 Presta24.eu - www.presta24.eu
 * @license    GNU General Public License v2.0
 */

if (!defined('_PS_VERSION_')) {
	exit;
}

use PrestaShop\PrestaShop\Core\Product\ProductExtraContent;
require_once dirname(__FILE__) . '/classes/ImportUpdate.php';

class Alsoimport extends Module
{

	public function __construct()
	{
		$this->name          = 'alsoimport';
		$this->tab           = 'others';
		$this->version       = '1.0.0';
		$this->author        = 'Presta24.eu';
		$this->need_instance = 1;

		$this->bootstrap = true;

		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
		$this->secure_key             = Tools::encrypt($this->name);

		if (Shop::isFeatureActive()) {
			Shop::addTableAssociation('alsoimport_shop', array('type' => 'shop'));
		}

		parent::__construct();

		$this->displayName = $this->l('Also import');
		$this->description = $this->l('Also API import module ');

		$this->confirmUninstall = $this->l('Are you sure you want to delete?');
	}

	public function install()
	{

		if (Shop::isFeatureActive()) {
			Shop::setContext(Shop::CONTEXT_ALL);
		}

		return (parent::install()
			&& $this->prepareModuleSettings()
			&& Configuration::updateValue('ALSOIMPORT_CLIENTID', false)
			&& Configuration::updateValue('ALSOIMPORT_USERNAME', false)
			&& Configuration::updateValue('ALSOIMPORT_PASSWORD', false)
			&& Configuration::updateValue('ALSOIMPORT_TAXGROUP', false)
			&& Configuration::updateValue('ALSOIMPORT_TAX_INIT', false)
            && Configuration::updateValue('ALSOIMPORT_SCI', false)
			&& $this->registerHook('header')
			&& $this->registerHook('backOfficeHeader')
			&& $this->registerHook('actionAttributeDelete')
			&& $this->registerHook('actionAttributeGroupDelete')
			&& $this->registerHook('actionFeatureDelete')
			&& $this->registerHook('actionFeatureValueDelete')
			&& $this->registerHook('actionFeatureValueSave')
			&& $this->registerHook('actionObjectCategoryAddAfter')
			&& $this->registerHook('actionObjectProductAddAfter')
			&& $this->registerHook('actionOrderDetail')
			&& $this->registerHook('actionOrderStatusPostUpdate')
			&& $this->registerHook('actionOrderStatusUpdate')
			&& $this->registerHook('actionProductAdd')
			&& $this->registerHook('actionProductAttributeDelete')
			&& $this->registerHook('actionProductAttributeUpdate')
			&& $this->registerHook('actionProductCancel')
			&& $this->registerHook('actionProductDelete')
			&& $this->registerHook('actionProductListOverride')
			&& $this->registerHook('actionProductSave')
			&& $this->registerHook('actionProductUpdate')
			// && $this->alterTable('add')
			 && $this->registerHook('actionAdminControllerSetMedia')
			&& $this->registerHook('actionUpdateQuantity')
			&& $this->registerHook('actionValidateOrder')
			&& $this->registerHook('displayOrderConfirmation')
			&& $this->registerHook('actionObjectProductDeleteAfter')
			&& $this->registerHook('displayAdminProductsExtra')
			&& $this->registerHook('actionObjectProductUpdateAfter'));
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function prepareModuleSettings()
	{
		include dirname(__file__) . '/sql/install.php';

		//Tab Also import filter

		$parent_tab = new Tab();

		$parent_tab->name = array();
		foreach (Language::getLanguages(true) as $lang) {
			$parent_tab->name[$lang['id_lang']] = $this->l('Also import');
		}

		$parent_tab->class_name = 'AlsoImport';
		$parent_tab->id_parent  = 0;
		$parent_tab->active     = 1;
		$parent_tab->module     = $this->name;
		$parent_tab->add();

		//Tab Also import Settings
		$tab = new Tab();

		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = $this->l('Alsoimport settings');
		}

		$tab->class_name = 'AdminAlsoSettings';
		$tab->id_parent  = $parent_tab->id;
		$tab->active     = 1;
		$tab->module     = $this->name;
		$tab->add();

		//Tab Also import filter
		$tab = new Tab();

		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = $this->l('Alsoimport filter');
		}

		$tab->class_name = 'AdminAlsoImport';
		$tab->id_parent  = $parent_tab->id;
		$tab->active     = 1;
		$tab->module     = $this->name;
		$tab->add();

		$id_lang = $this->context->language->id;

		/**

		For theme developers - you're welcome!

		 **/
		if (file_exists(_PS_MODULE_DIR_ . 'alsoimport/sql/my-install.php')) {
			include_once _PS_MODULE_DIR_ . 'alsoimport/sql/my-install.php';
		}

		return true;
	}

	public function installTab($className, $tabName, $tabParentName = false)
	{
		$tab             = new Tab();
		$tab->active     = 1;
		$tab->class_name = $className;
		$tab->name       = array();

		foreach (Language::getLanguages(true) as $lang) {
			$tab->name[$lang['id_lang']] = $tabName;
		}
		if ($tabParentName) {
			$tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
		} else {
			$tab->id_parent = 0;
		}
		$tab->module = $this->name;
		return $tab->add();
	}

	public function uninstall()
	{
		if (!parent::uninstall()) {
			return false;
		}

		Configuration::deleteByName('ALSOIMPORT_CLIENTID')
		&& Configuration::deleteByName('ALSOIMPORT_USERNAME')
		&& Configuration::deleteByName('ALSOIMPORT_PASSWORD')
		&& Configuration::deleteByName('ALSOIMPORT_TAXGROUP')
		&& Configuration::deleteByName('ALSOIMPORT_TAX_INIT')
    && Configuration::deleteByName('ALSOIMPORT_SCI');

		// Database
		include dirname(__file__) . '/sql/uninstall.php';

		// Tabs
		$idTabs   = array();
		$idTabs[] = Tab::getIdFromClassName('AdminAlsoImport');
		$idTabs[] = Tab::getIdFromClassName('AdminAlsoSettings');
		$idTabs[] = Tab::getIdFromClassName('AdminAlsoReplace');

		foreach ($idTabs as $idTab) {
			if ($idTab) {
				$tab = new Tab($idTab);
				$tab->delete();
			}
		}

		// For theme developers - you're welcome!
		if (file_exists(_PS_MODULE_DIR_ . 'alsoimport/sql/my-uninstall.php')) {
			include_once _PS_MODULE_DIR_ . 'alsoimport/sql/my-uninstall.php';
		}

		return true;
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		$this->smarty->assign('module_path', _MODULE_DIR_ . 'alsoimport/');
		return $this->display(__FILE__, 'views/templates/admin/welcome.tpl');
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookBackOfficeHeader()
	{
		//  if (Tools::getValue('configure') == $this->name) {
		$this->context->controller->addJS($this->_path . 'views/js/back.js');
		$this->context->controller->addCSS($this->_path . 'views/css/also-configure-ps-16.css');
		// }
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		$this->context->controller->addJS($this->_path . '/views/js/front.js');
		$this->context->controller->addCSS($this->_path . '/views/css/front.css');
	}

	public function hookDisplayAdminProductsExtra($params)
	{
		/*$idProduct = (int) Tools::getValue('id_product', $params['id_product']);
		$this->context->smarty->assign(array(
			'idProduct ' => $idProduct,
			'also_field' => $this->getCustomField($idProduct),
			'selected_posts' => $selected_posts,
			'module_path' => $this->_path,
			'secure_key' => $this->secure_key,
			'path' => $this->_path,
		));
		return $this->display(__FILE__, 'views/templates/admin/admin-tab.tpl');*/
	}

	public function hookActionProductUpdate($params)
	{
		$id_product = (int) Tools::getValue('id_product');
		Db::getInstance()->update('product', array(
			'skipname' => (int) (pSQL(Tools::getValue('skipname'))),
			'skipstatus' => (int) (pSQL(Tools::getValue('skipstatus'))),
			'skipdescr' => (int) (pSQL(Tools::getValue('skipdescr'))),
			'skipimage' => (int) (pSQL(Tools::getValue('skipimage'))),
			'skipcat' => (int) (pSQL(Tools::getValue('skipcat'))),
			'skipstock' => (int) (pSQL(Tools::getValue('skipstock'))),
			'skipprice' => (int) (pSQL(Tools::getValue('skipprice'))),
			'skipseo' => (int) (pSQL(Tools::getValue('skipseo'))),
		), 'id_product = "' . (int) $id_product . '"');
	}

	public function getCustomField($id_product)
	{
	 $result = Db::getInstance()->executeS('SELECT skipname,skipstatus,skipdescr,skipimage,skipcat,skipstock,skipprice,skipseo FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . (int) $id_product);
		if (!$result) {
			return array();
		}

		foreach ($result as $field) {
			$cron_also['skipname']   = $field['skipname'];
			$cron_also['skipstatus'] = $field['skipstatus'];
			$cron_also['skipdescr']  = $field['skipdescr'];
			$cron_also['skipimage']  = $field['skipimage'];
			$cron_also['skipcat']    = $field['skipcat'];
			$cron_also['skipstock']  = $field['skipstock'];
			$cron_also['skipprice']  = $field['skipprice'];
			$cron_also['skipseo']    = $field['skipseo'];
		}

		return $cron_also;
	}

	public function getAlsoUser()
	{
/*
		$AlsoUserData = array(
			'CompanyId' => '_al',
			'LicenseKey' => Configuration::get('ALSOIMPORT_KEY')

		);
		//$alsoUser->post('http://api.accdistribution.net/v1/GetDeliveryAddresses', array('request' => $data));
		$payload = json_encode(array('request' => $AlsoUserData));

		// Prepare new cURL resource
		$ch = curl_init('http://api.accdistribution.net/v1/GetDeliveryAddresses');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		// Set HTTP Header for POST request
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($payload))
		);

		// Submit the POST request
		$alsoUser = curl_exec($ch);

		// Close cURL session handle
		curl_close($ch);

		$result = json_decode($alsoUser);

		return $result->value['0']->CustomerId;
*/
	}

	public function hookActionValidateOrder($params)
	{
		/*
		$context = Context::getContext();
		$id_lang = (int) $context->language->id;
		$id_shop = (int) $context->shop->id;

		$orders_param = $params['order'];

		//Get data from $id_order where product_reference is like AC_ goods;
		$id_order = $orders_param->id;
	
		$sql = '
        SELECT a.id_order,a.product_id,a.product_reference,a.product_quantity,b.alsopid
        FROM `' . _DB_PREFIX_ . 'order_detail` AS a
        INNER JOIN `' . _DB_PREFIX_ . 'product` AS b ON b.id_product = a.product_id
        WHERE a.id_order = "' . $id_order . '" AND b.alsopid LIKE "AC_%"
        ORDER BY
        a.product_id ASC';
		//if data exist in sql query
		if ($orders = Db::getInstance()->ExecuteS($sql)) {
			$order_infos['BillingPaysCashOnDelivery'] = false;
			$order_infos['DlvPointId']                = $this->getAlsoUser();
			$order_infos['DlvChannel']                = '10';
			$order_infos['DlvType']                   = '0'; //Do not send now, keep reservation up to 3 days.
			$order_infos['CallBeforeDelivery']        = false;

			// Create empty array to hold query results
			$order_line = [];
			// Loop through query and push results into $order_info;

			foreach ($orders as $key => $order) {
				array_push($order_line, [
					'ProductId' => substr($order['alsopid'], 3),
					'Quantity' => (int) $order['product_quantity'],
					'BillingLineNote' => "Store Order ID $id_order",
					'SmartPoints' => 0,
				]);
			}
			//Collect data to array
			$orderalsodata = array('request' => array(
				'CompanyId' => '_al',
				'LicenseKey' => Configuration::get('ALSOIMPORT_KEY'),
				'OrderInfo' => $order_infos,
				'OrderLines' => $order_line,
			)
			);
			$payorder = json_encode($orderalsodata);
			//Post curl json
			$ch = curl_init('https://api.accdistribution.net/v1/Orders/Create');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payorder);

			// Set HTTP Header for POST request
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($payorder))
			);

			// Submit the POST request
			$orderalso = curl_exec($ch);

			// Close cURL session handle
			curl_close($ch);
			$resultOrder = json_decode($orderalso);

			//Send mail about order create in ALSO
  	Mail::Send(
				(int) (Configuration::get('PS_LANG_DEFAULT')), // defaut language id
				'alsoorder', // email template file to be use
				//' ALSO order created.', // email subject
				$this->trans('ALSO order created.', array(), 'Modules.Alsoimport.Shop'),
				array(
					'{email}' => Configuration::get('PS_SHOP_EMAIL'), // sender email address
					'{message}' => 'YOUR_SHOP_NAME order No. <strong>' . $id_order . '</strong> ALSO order created in the system - number ' . $resultOrder->OrderId . '. Reserved goods for 3 days' // email content
				),
				Configuration::get('PS_SHOP_EMAIL'), // receiver email address
				null, //receiver name
				Configuration::get('PS_SHOP_EMAIL'), //from email address
				null//from name
			);

			// $orderalso->close();
			//End if data exist in sql query
		}
*/
	}

	public static function isPHPCLI()
	{
		if (method_exists("Tools", "isPHPCLI")) {
			return Tools::isPHPCLI();
		} else {
			return (defined('STDIN') || (Tools::strtolower(php_sapi_name()) == 'cli' && (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR']))));
		}
	}
}
