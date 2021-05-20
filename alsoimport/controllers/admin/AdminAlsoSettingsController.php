<?php
require_once _PS_MODULE_DIR_ . 'alsoimport/alsoimportas.php';

class AdminAlsoSettingsController extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;

        $this->initOptions();
    }

    public function initOptions()
    {
        $this->optionTitle = $this->l('Settings');
            
        $also_options = array(
            'general' => array(
                'title' =>  $this->l('Also import - Settings'),
                'image' =>   '../img/t/AdminOrderPreferences.gif',
              //  'info' => $also_settings_content,
                'fields' => array(

                    'ALSOIMPORT_CLIENTID' => array(
                        'title' => $this->l('Also Client ID'),
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Also Client ID from distributor'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => true,
                      //  'validation' => 'isUnsignedId',
                    ),
                    'ALSOIMPORT_USERNAME' => array(
                        'title' => $this->l('Also username'),
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Also username from distributor'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => true,
                      //  'validation' => 'isUnsignedId',
                    ),
                    'ALSOIMPORT_PASSWORD' => array(
                        'title' => $this->l('Also password'),
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Also password from distributor'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => true,
                      //  'validation' => 'isUnsignedId',
                    ),
                    'ALSOIMPORT_SCI' => array(
                        'title' => $this->l('Also SCI'),
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Also SCI from distributor'),
                        'type' => 'text',
                        'size' => 255,
                        'required' => true,
                      //  'validation' => 'isUnsignedId',
                    ),
                    'ALSOIMPORT_TAXGROUP' => array(
                        'title' => $this->l('Tax rule'),
                        'desc' => $this->l('Tax rule for import'),
                        'type' => 'select',
                        'list' => TaxRulesGroup::getTaxRulesGroupsForOptions((int)$this->context->language->id),
                        'identifier' => 'id_tax_rules_group',
                        'required' => false,
                        'validation' => 'isGenericName',

                    ),
                    'ALSOIMPORT_TAX_INIT' => array(
                        'title' => $this->l('Add tax to import goods?'),
                        'validation' => 'isBool',
                        'cast' => 'intval',
                        'desc' => $this->l('If yes, import add tax to original prices.'),
                        'required' => true,
                        'type' => 'bool'
                    ),
                ),
                'submit' => array('title' => $this->l('Update'), 'class' => 'button'),
            ),


        );


        //$this->hide_multishop_checkbox = true;
        $this->fields_options = array_merge($also_options);

        return parent::renderOptions();
    }


    public function initContent()
    {

        if(Tools::isSubmit('importAlsoTree'))
        {
          //  AdminAlsoSettingsController::importAlsoTree();
            Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token'));
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
        ));

        parent::initContent();
    }
}
