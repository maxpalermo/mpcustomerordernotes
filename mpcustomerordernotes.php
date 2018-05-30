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
require_once _PS_MODULE_DIR_.'mpcustomerordernotes/classes/MpCustomerOrderNotesPrintReport.php';

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
        $this->module_key = "f197a003c4b0f405c4194b4472237623";
        /** CONSTRUCT **/
        parent::__construct();
        /** OTHER CONFIG **/
        if (!isset($this->context->employee)) {
            $cookie = new Cookie("psAdmin");
            $this->id_employee = $cookie->id_employee;
        } else {
            $this->id_employee = (int) $this->context->employee->id;
        }
        $this->adminClassName = 'AdminMpCustomerOrderNotes';
        $this->displayName = $this->l('MP Customer order notes');
        $this->description = $this->l('With this module you can add private notes to your orders.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->context = Context::getContext();
        $this->smarty = $this->context->smarty;
        $this->id_lang = (int) $this->context->language->id;
        $this->id_shop = (int) $this->context->shop->id;
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
            );

            print $l_delimiter;
            print Tools::jsonEncode($response);
            print $r_delimiter;
            exit();
        }

        if (Tools::isSubmit('ajax') && Tools::getValue('action') == 'refreshTableCustomerNotes') {
            $table = new MpCustomerOrderNotesObjectModel($this);
            $l_delimiter = "!!START!!";
            $r_delimiter = "!!END!!";

            print $l_delimiter;
            print $table->getTable();
            print $r_delimiter;
            exit();
        }

        $template = $this->getAdminTemplatePath().'customer_messages.tpl';
        $table = new MpCustomerOrderNotesObjectModel($this);
        $shop  = new ShopCore($this->id_shop);
        $url = $shop->getBaseURI();
        $this->smarty->assign(
            array(
                'currentindex' => $url.'modules/mpcustomerordernotes/printReport.php',
                'customer_order_table' => $table->getTable(),
                'tot_notes' => $table->getTotNotes(),
                'id_order' => Tools::getValue('id_order', 0),
                'id_employee' => $this->id_employee,
            )
        );
        return $this->smarty->fetch($template);
    }

    public function hookDisplayAdminOrderContentOrder()
    {
        //TODO
    }

    public function hookDisplayAdminOrderContentShip()
    {
        //TODO
    }

    public function hookDisplayAdminOrderTabOrder()
    {
        //TODO
    }

    public function hookDisplayAdminOrderTabShip()
    {
        //TODO
    }

    public function printCustomerOrderNote()
    {
        //$PDF_HEADER_LOGO = dirname(__FILE__).'/views/img/tcpdf.jpg';
        $table = new MpCustomerOrderNotesObjectModel($this, 0, $this->id_employee);
        $table->id_order = (int)Tools::getValue('id_order', 0);
        $order = new Order($table->id_order);
        $customer = new Customer($order->id_customer);

        $list = $table->getList(true);
        $this->smarty->assign(
            array(
                'table_data' => $list,
                'id_order' => $table->id_order,
                'date_order' => Tools::displayDate($order->date_add),
                'customer' => Tools::strtoupper($customer->firstname.' '.$customer->lastname),
            )
        );
        $html = $this->smarty->fetch($this->getAdminTemplatePath().'pdf_table.tpl');

        //print $html;
        //exit();

        $pageSize = array(210,297);
        $pdf = new MpCustomerOrderNotesPrintReport("P", "mm", $pageSize, true, "UTF-8", false, false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Massimiliano Palermo');
        $pdf->SetTitle(
            sprintf(
                $this->l('Customer Order notes for order %s'),
                Tools::getValue('id_order', '')
            )
        );
        $pdf->SetSubject('TCPDF Report');
        $pdf->SetKeywords('TCPDF, PDF, report, massimiliano palermo, digital solutions, mpcustomerordernote');

        // set default header data
        /*
        $pdf->SetHeaderData(
            $PDF_HEADER_LOGO,
            300,
            '{l s='Order Notes' mod='mpcustomerordernote'}',
            '{l s='Order Notes' mod='mpcustomerordernote'}'
        );
        */
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('helvetica', 'B', 20);

        // add a page
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8);
        $pdf->writeHTML($html, true, false, false, false, '');

        //ob_end_clean();
        print $pdf->Output('report.pdf', "I");

        exit();
    }
}
