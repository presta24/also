<?php
/*
 * @author    Presta24 <info@presta24.com>
 * @site	  www.presta24.eu
 * @copyright  Copyright (c) 2021 Presta24 - www.presta24.eu
 */

require_once _PS_ROOT_DIR_ . '/modules/alsoimport/alsoimport.php';
require_once _PS_ROOT_DIR_ . '/modules/alsoimport/classes/ALSO.php';

class AdminAlsoImportController extends ModuleAdminController {
	protected $_module             = null;
	protected $local_path          = _PS_MODULE_DIR_;
	protected $_path               = _PS_MODULE_DIR_;
	protected $position_identifier = 'id_alsoimport';

	public function __construct() {
		$this->table     = 'alsoimport';
		$this->className = 'AdminAlsoImportController';
		$this->context   = Context::getContext();
		$id_shop         = (int) $this->context->shop->id;
  $this->_join     = 'LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` c ON (a.`presta_cat` = c.`id_category` AND c.`id_lang` = ' . (int) $this->context->language->id . ' AND c.`id_shop` = ' . $id_shop . ')';
		//$this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'alsoimportas_tree` d ON (d.`also_cat_id` = a.`also_cat`) ';
		$this->_select         = 'a.*,c.*';
		$this->className       = 'ALSO';
		$this->_defaultOrderBy = 'id_alsoimport';
  $this->lang      = false;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
  $this->bulk_actions = array(
        'delete' => array('text' => $this->l('Delete selected'),'icon' => 'icon-power-off text-success', 'confirm' => $this->l('Delete selected?')),
  );
		Shop::addTableAssociation($this->table, array(
			'type' => 'shop'
		));

		$this->fields_list = array(
			'id_alsoimport' => array(
				'title' => $this->l('ID'),
				'align' => 'left',
				'width' => '30'
			),

			'also_cat' => array(
				'title' => $this->l('Also categories'),
				'width' => 'auto',
				'callback' => 'getMultipleAlsoCategory',
				'search' => false,
				'orderby' => false
			),

  	'presta_cat' => array(
				'title' => $this->l('Presta categories'),
				'width' => 'auto',
				'callback' => 'getMultipleCategoryName',
				'search' => false,
				'orderby' => false
			),

			'brand' => array(
			'title' => $this->l('Brands'),
   'callback' => 'getMultipleBrandName',
			'search' => false,
			'orderby' => false
			),

			'stock' => array(
				'title' => $this->l('Import delivery by order'),
				'width' => 'auto',
				'align' => 'center',
				/*'active' => 'status',
				'type' => 'bool',*/
				'orderby' => false
			),

			'margin1' => array(
				'title' => $this->l('Margin 1'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
   'margin2' => array(
				'title' => $this->l('Margin 2'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
   'margin3' => array(
				'title' => $this->l('Margin 3'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
   'margin4' => array(
				'title' => $this->l('Margin 4'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
   'margin5' => array(
				'title' => $this->l('Margin 5'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
	'margin6' => array(
				'title' => $this->l('Margin 6'),
				'align' => 'left',
				'width' => 'auto',
				'type' => 'percent',
    'search' => false,
			'orderby' => false
			),
			'margin7' => array(
        'title' => $this->l('Margin 7'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin8' => array(
        'title' => $this->l('Margin 8'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin9' => array(
        'title' => $this->l('Margin 9'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin10' => array(
        'title' => $this->l('Margin 10'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin11' => array(
        'title' => $this->l('Margin 11'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin12' => array(
        'title' => $this->l('Margin 12'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin13' => array(
        'title' => $this->l('Margin 13'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin14' => array(
        'title' => $this->l('Margin 14'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
      'margin15' => array(
        'title' => $this->l('Margin 15'),
        'align' => 'left',
        'width' => 'auto',
        'type' => 'percent',
        'search' => false,
        'orderby' => false
      ),
	'cron_date' => array(
		'title' => $this->l('Last import'),
		'align' => 'left',
		'width' => 'auto',
		'type' => 'datetime'
		),
			

			'complete' => array(
				'title' => $this->l('Cron complete'),
				'align' => 'center',
				'width' => 'auto',
				'active' => 'complete',
				'type' => 'bool',
				'activeVisu' => 'complete',
				'hint' => 'On - complete, Off - cron error',
				'orderby' => true
			),

   			'info' => array(
				'title' => $this->l('Note'),
				'width' => 'auto',
				'search' => false,
				'orderby' => false
			),

			'active' => array(
				'title' => $this->l('Status'),
				'width' => 'auto',
				'align' => 'left',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false
			)
		);

		$this->bootstrap = true;
		parent::__construct();
	}

 public function getMultipleAlsoCategory($value, $form) {
		$sql = '
  SELECT GROUP_CONCAT( `c`.`also_cat` SEPARATOR " <strong>&plus;</strong><br>" ) AS `also_cat`
  FROM `'._DB_PREFIX_.'alsoimport_tree` AS `c`
  WHERE
  FIND_IN_SET( `c`.`also_cat_id`, "'.$value.'" ) != 0';

  return Db::getInstance()->getValue($sql);
	}

	public function getMultipleCategoryName($value, $form) {
		$sql = '
  SELECT GROUP_CONCAT( `c`.`name` SEPARATOR " <strong>&plus;</strong><br>" ) AS `cats_name`
  FROM `' . _DB_PREFIX_ . 'category_lang` AS `c`
  WHERE
  FIND_IN_SET( `c`.`id_category`, "' . $value . '" ) != 0
  AND  `c`.`id_lang` = "'.Configuration::get('PS_LANG_DEFAULT').'"';

		return Db::getInstance()->getValue($sql);
	}

 public function getMultipleBrandName($value, $form) {
		$sql = '
  SELECT GROUP_CONCAT( `c`.`also_cat` SEPARATOR " | " ) AS `also_cat`
  FROM `'._DB_PREFIX_.'alsoimport_tree` AS `c`
  WHERE
  FIND_IN_SET( `c`.`also_cat_id`, "'.$value.'" ) != 0';

  return Db::getInstance()->getValue($sql);
	}

	public function l($string, $class = null, $addslashes = false, $htmlentities = true) {
		if (is_null($this->_module)) {
			$this->_module = new alsoimport();
		}

		return $this->_module->l($string, __class__);
	}

	public function renderForm() {
		$this->display = 'edit';
		$this->initToolbar();
		$obj = $this->loadObject(true);
		//$this->getImportUpdateForm();
		// echo "<pre>";
		//  echo $obj->presta_cat."-".$obj->also_cat."-".$obj->brand;
		// print_R($obj);
		/**
		 *
		 * Get category list
		 *
		 **/
		$root = Category::getRootCategory();
		$sql  = 'SELECT `presta_cat`
        FROM `' . _DB_PREFIX_ . 'alsoimport` AS d
        WHERE `id_alsoimport` ="' . (int) (Tools::getValue('id_alsoimport')) . '"';
		$_selectedCat = explode(",", Db::getInstance()->getValue($sql));
		//Generating the tree for the first column
		$tree = new HelperTreeCategories('categories_col1');
		$tree->setUseCheckBox(true)
			->setUseSearch(true)
			->setAttribute('is_category_filter', $root->id)
			->setRootCategory($root->id)
			->setSelectedCategories($_selectedCat)
			->setInputName('presta_cat');
		$presta_cat = $tree->render();

//RRP options
		$stockoptions = array(
			array(
				'id_option' => 'All',
				'name' => 'All'
			),
			
			array(
				'id_option' => 'OnStock',
				'name' => 'On Stock'
			),

			array(
				'id_option' => 'Transit',
				'name' => 'Transit'
			),
		);

		$this->fields_value['presta_cat[]'] = explode(',', $obj->presta_cat);
		$this->fields_value['brand[]']      = explode(',', $obj->brand);
  $this->fields_value['also_cat[]']   = explode(',', $obj->also_cat);

  $this->fields_form = array(
			'tinymce' => false,
			'legend' => array(
				'title' => $this->l('Also import filter'),
				'image' => '/modules/alsoimport/views/img/admin/icon-filter.png'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('ID:'),
					'name' => 'id_alsoimport',
					'id' => 'id_alsoimport',
					'is_invisible' => false
				),
				/* array(
				'type' => 'text',
				'label' => $this->l('Title:'),
				'name' => 'also_name',
				'id' => 'name',
				'lang' => false,
				'required' => TRUE,
				'hint' => $this->l('Please enter filter name'),
				'size' => 10
				),
				 */
				array(
					'type' => 'categories_select',
					'label' => $this->l('Presta category:'),
					'desc' => $this->l('Choose a shop category'),
					'name' => 'presta_cat[]',
					'multiple' => true,
					'required' => true,
					'hint' => $this->l('Please select Prestashop category where to import'),
					'category_tree' => $presta_cat
				),
				array(
					'type' => 'select',
					'label' => $this->l('Also category:'),
					'desc' => $this->l('Choose an Also category'),
					'name' => 'also_cat[]',
					'required' => true,
					'hint' => $this->l('Choose an Also category to filter for goods'),
     'col' => '6',
					'class' => 'chosen',
					'multiple' => true,
					'options' => array(
						'query' => Also::getAlsoCategory(),
						'id' => 'value',
						'name' => 'name',
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('Also Vendor:'),
					'desc' => $this->l('Choose a Also Vendor'),
					'name' => 'brand[]',
					'required' => false,
					'col' => '6',
					'class' => 'chosen',
					'multiple' => true,
					//'filter_key' => 'e!brand',
					'hint' => $this->l('Choose a Also Vendor to filter for goods'),
					'options' => array(
						'query' => Also::getAlsoVendor(true),
						'id' => 'id_brand',
						'name' => 'brands',
					)
				),

				array(
					'type' => 'select',
					'label' => $this->l('Stock Level:'),
					'desc' => $this->l('Filtering based on the physical state of product'),
					'name' => 'stock',
					'required' => true,
					'options' => array(
						'query' => $stockoptions,
						'id' => 'id_option',
						'name' => 'name',
						'default' => array(
							'value' => '',
							'label' => $this->l('--Select--')
						)
					)
				),
				array(
          'type' => 'text',
          'label' => $this->l('Margin < 10 EUR:'),
          'name' => 'margin1',
          'id' => 'margin1',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 10 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 20 EUR:'),
          'name' => 'margin2',
          'id' => 'margin2',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 20 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 30 EUR:'),
          'name' => 'margin3',
          'id' => 'margin3',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 30 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 40 EUR'),
          'name' => 'margin4',
          'id' => 'margin4',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 40 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 50 EUR'),
          'name' => 'margin5',
          'id' => 'margin5',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 50 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 60 EUR'),
          'name' => 'margin6',
          'id' => 'margin6',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 60 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 70 EUR'),
          'name' => 'margin7',
          'id' => 'margin7',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 70 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 80 EUR'),
          'name' => 'margin8',
          'id' => 'margin8',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 80 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 90 EUR'),
          'name' => 'margin9',
          'id' => 'margin9',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 90 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 100 EUR'),
          'name' => 'margin10',
          'id' => 'margin10',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 100 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 200 EUR'),
          'name' => 'margin11',
          'id' => 'margin11',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 200 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 300 EUR'),
          'name' => 'margin12',
          'id' => 'margin12',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 300 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 400 EUR'),
          'name' => 'margin13',
          'id' => 'margin13',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 400 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin < 500 EUR'),
          'name' => 'margin14',
          'id' => 'margin14',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is less than 500 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
        array(
          'type' => 'text',
          'label' => $this->l('Margin > 500 EUR'),
          'name' => 'margin15',
          'id' => 'margin15',
          'lang' => false,
          'required' => true,
          'hint' => $this->l('If the price is more than 500 EUR added the specified markup %'),
          'suffix' => ' %',
          'size' => 20
        ),
				array(
					'type' => 'textarea',
					'label' => $this->l('Note'),
					'name' => 'info',
					'autoload_rte' => false,
					'lang' => false,
					'required' => false,
					'rows' => 5,
					'cols' => 40,
					'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Active:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'require_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'require_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				)
			),
			'buttons' => array(
				'save-and-stay' => array(
					'title' => $this->l('Save and Stay'),
					'name' => 'submitAdd' . $this->table . 'AndStay',
					'type' => 'submit',
					'class' => 'btn btn-default pull-right',
					'icon' => 'process-icon-save'
				)
			),

			'submit' => array(
				'title' => $this->l('Save')
			)

		);

		if (Shop::isFeatureActive()) {
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso'
			);
		}

		return parent::renderForm() . $this->getImportUpdateForm();
	}

	public function postProcess() {
		// print_r(Tools::getValue('presta_cat'));

		if (Tools::isSubmit('submitAddalsoimport')) {
			$_POST['presta_cat'] = implode(',', Tools::getValue('presta_cat'));
   $_POST['also_cat'] = implode(',', Tools::getValue('also_cat'));

			if (Tools::getValue('brand')) {
				$_POST['brand'] = implode(',', Tools::getValue('brand'));
			} else {
				$_POST['brand'] = '';
			}
		}
		parent::postProcess();
	}

//Get also filter data
	public function getImportUpdateForm() {
		$this->context->smarty->assign('module_dir', $this->_path);
		$this->context->smarty->assign(array(
			'baseURI' => __PS_BASE_URI__,
			'itoken' => Tools::substr(_COOKIE_KEY_, 34, 8),
			'obj' => $this->loadObject(true),
		));
		$output = $this->context->smarty->fetch(($this->_path) . 'alsoimport/views/templates/admin/importnow.tpl');
		return $output;
	}
}