<?php
/**
* 2007-2018 PrestaShop
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
*  @author    Massimiliano Palermo <mpsoft.it>
*  @copyright 2018 Digital Solution®
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'mpcustomerordernotes/classes/MpCustomerOrderNotesObjectModel.php';

class MpCustomerOrderNotes extends Module
{
    public $config_form = false;
    public $adminClassName;
    public $id_lang;
    public $id_shop;
    public $link;
    public $smarty;
    public $context;
    private $errors = array();
    private $warnings = array();
    private $confirmations = array();

    public function __construct()
    {
        $this->name = 'mpcustomerordernotes';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Digital Solutions®';
        $this->need_instance = 0;
        $this->bootstrap = true;
        /** CONSTRUCT **/
        parent::__construct();
        /** OTHER CONFIG **/
        $this->adminClassName = 'AdminMpCustomerOrderNotes';
        $this->displayName = $this->l('MP Customer order notes');
        $this->description = $this->l('With this module you can add private notes to your orders.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->context = Context::getContext();
        $this->smarty = $this->context->smarty;
        $this->id_lang = (int) $this->context->language->id;
        $this->id_shop = (int) $this->context->shop->id;
        $this->id_employee = (int) $this->context->employee->id;
        $this->link = $this->context->link;
    }
    
    /**
     * Return the admin class name
     * @return string Admin class name
     */
    public function getAdminClassName()
    {
        return $this->adminClassName;
    }
    
    /**
     * Return the Admin Template Path
     * @return string The admin template path
     */
    public function getAdminTemplatePath()
    {
        return $this->getPath().'views/templates/admin/';
    }
    
    /**
     * Get the Id of current language
     * @return int id language
     */
    public function getIdLang()
    {
        return (int)$this->id_lang;
    }
    
    /**
     * Get the Id of current shop
     * @return int id shop
     */
    public function getIdShop()
    {
        return (int)$this->id_shop;
    }
    
    /**
     * Get The URL path of this module
     * @return string The URL of this module
     */
    public function getUrl()
    {
        return $this->_path;
    }
    
    /**
     * Return the physical path of this module
     * @return string The path of this module
     */
    public function getPath()
    {
        return $this->local_path;
    }

    /**
     * Add a message to Errors collection
     * @param string $message Message to add to collection
     */
    public function addError($message)
    {
        $this->_errors[] = $this->displayError($message);
    }
    
    /**
     * Add a message to Warnings collection
     * @param string $message Message to add to collection
     */
    public function addWarning($message)
    {
        $this->_errors[] = $this->displayWarning($message);
    }
    
    /**
     * Add a message to Confirmations collection
     * @param string $message Message to add to collection
     */
    public function addConfirmation($message)
    {
        $this->_confirmations[] = $message;
    }
    
    /**
     * Check if there is an Ajax call and execute it.
     */
    public function ajax()
    {
        if (Tools::isSubmit('ajax') && Tools::isSubmit('action')) {
            $action = 'ajaxProcess' . Tools::ucfirst(Tools::getValue('action'));
            $this->$action();
            exit();
        }
    }

    public function install()
    {
        return parent::install() &&
            $this->installSQL() &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayAdminOrderContentOrder') &&
            $this->registerHook('displayAdminOrderContentShip') &&
            $this->registerHook('displayAdminOrderTabOrder') &&
            $this->registerHook('displayAdminOrderTabShip') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->installTab('MpModules', $this->adminClassName, $this->l('MP Customer order Notes'));
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallTab($this->adminClassName);
    }
    
    public function installSQL()
    {
        return MpCustomerOrderNotesObjectModel::installSQL($this);
    }
    
    /**
     * Install a new menu
     * @param string $parent Parent tab name
     * @param type $class_name Class name of the module
     * @param type $name Display name of the module
     * @param type $active If true, Tab menu will be shown
     * @return boolean True if successfull, False otherwise
     */
    public function installTab($parent, $class_name, $name, $active = 1)
    {
        // Create new admin tab
        $tab = new Tab();
        
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name      = array();
        
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }
        
        $tab->class_name = $class_name;
        $tab->module     = $this->name;
        $tab->active     = $active;
        
        if (!$tab->add()) {
            $this->addError($this->l('Error during Tab install.'));
            return false;
        }
        return true;
    }
    
    /**
     * Uninstall a menu
     * @param string pe $class_name Class name of the module
     * @return boolean True if successfull, False otherwise
     */
    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab) {
            $tab = new Tab((int)$id_tab);
            return $tab->delete();
        }
    }
    
    public function hookDisplayBackOfficeHeader()
    {
        $ctrl = $this->context->controller;
        if ($ctrl instanceof AdminOrdersController) {
            //$this->context->controller->addJqueryUI('ui.accordion');
        }
    }
    
    public function hookDisplayAdminOrder()
    {
        if (Tools::isSubmit('ajax') && Tools::getValue('action') == 'addCustomerOrderMessage') {
            $id_order = (int)Tools::getValue('id_order', 0);
            $id_employee = (int)Tools::getValue('id_employee', 0);
            $date_add = Tools::getValue('date_add');
            $date_json = Tools::jsonDecode(Tools::getValue('date_json'));
            $content = Tools::getValue('content');
            $l_delimiter = "!!START!!";
            $r_delimiter = "!!END!!";
            $object = new MpCustomerOrderNotesObjectModel($this);
            $object->id_order = $id_order;
            $object->id_employee = $id_employee;
            $object->date_add = $date_add;
            $object->content = pSQL(trim($content));
            $result = (int)$object->add();
            $response = array(
                'result' => $result,
                'id_order' => $id_order,
                'id_employee' => $id_employee,
                'date_add' => $date_add,
                'content' => $content,
                'object' => Tools::jsonEncode($object),
            );

            print $l_delimiter;
            print Tools::jsonEncode($response);
            print $r_delimiter;
            exit();
        }


        $template = $this->getAdminTemplatePath().'customer_messages.tpl';
        $table = new MpCustomerOrderNotesObjectModel($this);

        $this->smarty->assign(
            array(
                'customer_order_table' => $table->getTable(),
                'id_order' => Tools::getValue('id_order', 0),
                'id_employee' => $this->id_employee,
            )
        );
        return $this->smarty->fetch($template);
    }

    public function hookDisplayAdminOrderContentOrder()
    {
        return;
    }

    public function hookDisplayAdminOrderContentShip()
    {
        return;
    }

    public function hookDisplayAdminOrderTabOrder()
    {
        return;
    }

    public function hookDisplayAdminOrderTabShip()
    {
        return;
    }
}
