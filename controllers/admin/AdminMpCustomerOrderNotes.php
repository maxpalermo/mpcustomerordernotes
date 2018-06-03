<?php
/**
 * 2017 mpSOFT
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
 *  @author    Massimiliano Palermo <info@mpsoft.it>
 *  @copyright 2018 Digital Solutions®
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of mpSOFT
 */

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
ini_set('post_max_size', '128M');
ini_set('upload_max_filesize', '128M');

require_once _PS_MODULE_DIR_.'mpcustomerordernotes/classes/MpCustomerOrderNotesAdmin.php';


class AdminMpCustomerOrderNotesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'AdminMpCustomerOrderNotes';
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();
        $this->token = Tools::getAdminTokenLite($this->className);
        $this->id_lang = (int)$this->context->language->id;
        $this->id_shop = (int)$this->context->shop->id;
        $this->id_employee = (int)$this->context->employee->id;
        $this->link = $this->context->link;
        $this->module = $this->context->controller->module;
        $this->smarty = $this->context->smarty;
        $this->table_name = 'mp_customer_order_notes';
    }

    public function addError($message)
    {
        $this->errors[] = Tools::displayError($message);
    }

    public function addWarning($message)
    {
        $this->warnings[] = $this->displayWarning($message);
    }

    public function addConfirmation($message)
    {
        $this->confirmations[] = $message;
    }

    public function setMedia()
    {
        parent::setMedia();
        if (Tools::getValue('controller') == $this->className) {
            $this->addJqueryUI('ui.dialog');
            $this->addJqueryUI('ui.progressbar');
            $this->addJqueryUI('ui.draggable');
            $this->addJqueryUI('ui.effect');
            $this->addJqueryUI('ui.effect-slide');
            $this->addJqueryUI('ui.effect-fold');
            $this->addJqueryUI('ui.autocomplete');
            $this->addJqueryUI('ui.datepicker');
            $this->addJqueryPlugin('growl');
        }
    }

    public function initContent()
    {
        $notes = new MpCustomerOrderNotesAdmin();
        $this->content = $notes->getTable();
        parent::initContent();
    }

    public function postProcess()
    {
        $submit = (int)Tools::isSubmit('delete'.$this->table_name);
        $update = (int)Tools::isSubmit('update'.$this->table_name);
        $deleted = (int)Tools::isSubmit('deleted'.$this->table_name);
        $printable = (int)Tools::isSubmit('printable'.$this->table_name);
        $chat = (int)Tools::isSubmit('chat'.$this->table_name);
        $id_note = (int)Tools::getValue('id_'.$this->table_name, 0);
        $note = new MpCustomerOrderNotesObjectModel($this->module, $id_note);
        $ordernote = new MpCustomerOrderNotesAdmin();

        if ($submit && $id_note) {
            $result = $note->delete();
            if ($result) {
                $this->addConfirmation($this->l('Message deleted.'));
            } else {
                $this->addError(sprintf($this->l('Error deleting message: %s'), Db::getInstance()->getMsgError()));
            }
        }

        if ($update && $id_note) {
            $link = $this->link->getAdminLink('AdminOrders')
                .'&id_order='.(int)$note->id_order
                .'&vieworder';
            ToolsCore::redirectAdmin($link);
            exit();
        }

        if ($deleted && $id_note) {
            $ordernote->toggleDeleted($id_note);
        }

        if ($printable && $id_note) {
            $ordernote->togglePrintable($id_note);
        }

        if ($chat && $id_note) {
            $ordernote->toggleChat($id_note);
        }
    }
}
