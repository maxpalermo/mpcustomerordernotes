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
*  @author    Massimiliano Palermo <info@mpsoft.it>
*  @copyright 2007-2018 Digital Solutions®
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MpCustomerOrderNotesAdmin
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->id_lang = (int)$this->context->language->id;
        $this->id_shop = (int)$this->context->shop->id;
        $this->id_employee = (int)$this->context->employee->id;
        $this->link = $this->context->link;
        $this->module = $this->context->controller->module;
        $this->table_name = 'mp_customer_order_notes';
        $this->className = 'AdminMpCustomerOrderNotes';
    }

    public function getEmployee()
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('firstname')
            ->select('lastname')
            ->from('employee')
            ->where('id_employee = ' . (int)$this->id_employee);
        $row = $db->getRow($sql);
        if ($row) {
            return $row['firstname'] . ' ' . $row['lastname'];
        } else {
            return "";
        }
    }

    private function l($message)
    {
        return $this->module->l($message, 'MpCustomerOrderNotesAdmin');
    }

    public function getTable()
    {
        //<!--Get filters-->
        $submitReset = (int)Tools::isSubmit('submitReset'.$this->table_name);
        $submitFilterTable = (int)Tools::getValue('submitFilter'.$this->table_name, 1);
        //$page = (int)Tools::getValue('page', 1);
        $pagination = (int)Tools::getValue('select_pagination', 20);
        $date = Tools::getValue($this->table_name."Filter_date_add", array());
        $id_order = (int)(int)Tools::getValue($this->table_name."Filter_id_order", 0);
        if ($date) {
            $date_start = $date[0];
            $date_end = $date[1];
        } else {
            $date_start = '';
            $date_end = '';
        }
        $employee = Tools::getValue($this->table_name."Filter_employee", '');
        //$submitFilter = (int)Tools::isSubmit('submitFilter');
        //<!--End -->

        $currentIndex = $this->link->getAdminLink($this->className, false);

        $helperList = new HelperList();
        $helperList->bootstrap = true;
        if ($this->context->employee->id_profile == 1) {
            $helperList->actions = array('delete');
        }
        $helperList->currentIndex = $currentIndex;
        $helperList->identifier = 'id_mp_customer_order_notes';
        $helperList->no_link = false;
        $helperList->page = $submitFilterTable;
        $helperList->_default_pagination = $pagination;
        $helperList->show_toolbar = true;
        $helperList->toolbar_btn = array(
            'print' => array(
                'desc' => '',
                'href' => 'javascript:void(0);',
            ),
        );
        $helperList->shopLinkType='';
        $helperList->simple_header = false;
        $helperList->token = Tools::getAdminTokenLite($this->className);
        $helperList->title = $this->l('Total notes:', $this->className);
        $helperList->table = 'mp_customer_order_notes';

        $list = $this->getRows($submitReset, $submitFilterTable, $pagination, $id_order, $date_start, $date_end, $employee);
        $helperList->listTotal = count($list);
        
        $fields_display = $this->getHeaders();

        return $helperList->generateList($list, $fields_display);
    }

    private function getHeaders()
    {
        $field_list = array(
            'id_mp_customer_order_notes' => array(
                'type' => 'text',
                'align' => 'right',
                'width' => 48,
                'title' => $this->l('Id', 'mpcustomerordernotes'),
                'search' => false,
            ),
            'id_order' => array(
                'type' => 'text',
                'align' => 'right',
                'width' => 48,
                'title' => $this->l('Id order', 'mpcustomerordernotes'),
                'search' => true,
            ),
            'order' => array(
                'type' => 'text',
                'align' => 'right',
                'width' => 48,
                'title' => $this->l('Order', 'mpcustomerordernotes'),
                'search' => false,
            ),
            'date_add' => array(
                'type' => 'datetime',
                'align' => 'left',
                'width' => 'auto',
                'title' => $this->l('Date add', 'mpcustomerordernotes'),
            ),
            'employee' => array(
                'type' => 'text',
                'align' => 'left',
                'width' => 'auto',
                'title' => $this->l('Employee', 'mpcustomerordernotes'),
            ),
            'content' => array(
                'type' => 'text',
                'align' => 'left',
                'width' => 'auto',
                'title' => $this->l('Message', 'mpcustomerordernotes'),
                'search' => false,
            ),
            'deleted' => array(
                'type' => 'bool',
                'align' => 'center',
                'width' => 'auto',
                'title' => $this->l('Deleted', 'mpcustomerordernotes'),
                'search' => false,
                'active' => 'deleted',
            ),
            'printable' => array(
                'type' => 'bool',
                'align' => 'center',
                'width' => 'auto',
                'title' => $this->l('Printable', 'mpcustomerordernotes'),
                'search' => false,
                'active' => 'printable',
            ),
        );

        return $field_list;
    }

    public function getRows($reset = true, $page = 1, $pagination = 20, $id_order = 0, $date_start = '', $date_end = '', $employee = '')
    {
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('n.*')
            ->select('e.firstname')
            ->select('e.lastname')
            ->select('o.reference as order_reference')
            ->select('o.date_add as order_date')
            ->from('mp_customer_order_notes', 'n')
            ->innerJoin('employee', 'e', 'e.id_employee=n.id_employee')
            ->innerJoin('orders', 'o', 'o.id_order=n.id_order')
            ->orderBy('n.date_add DESC');
        if (!$reset) {
            if ($date_start && $date_end) {
                $date_start .= ' 00:00:00';
                $date_end .= ' 23:59:59';
                $sql->where('date_add between \''.$date_start.'\' and \''.$date_end.'\'');
            } elseif ($date_start && !$date_end) {
                $date_start .= ' 00:00:00';
                $sql->where('date_add >= \''.$date_start.'\'');
            } elseif (!$date_start && $date_end) {
                $date_end .= ' 23:59:59';
                $sql->where('date_add <= \''.$date_end.'\'');
            }
            if ($employee) {
                $sql_emp = new DbQueryCore();
                $sql_emp->select('id_employee')
                    ->from('employee')
                    ->where("concat(`firstname`,' ', `lastname`) like '%".$employee."%'");
                $ids = $db->executeS($sql_emp);
                if ($ids) {
                    $arr = array();
                    foreach ($ids as $id) {
                        $arr[] = $id['id_employee'];
                    }
                    $id_employee = implode(',', $arr);
                    $sql->where('e.id_employee in ('.$id_employee.')');
                }
            }
            if ($id_order) {
                $sql->where('o.id_order = '.(int)$id_order);
            }
        }
        
        $page_start = ($page-1)*$pagination;
        $page_end = $page*$pagination;
        $sql->limit($page_start.','.$page_end);

        $result = $db->executeS($sql);
        if ($result) {
            foreach ($result as &$row) {
                $row['id'] = $row['id_mp_customer_order_notes'];
                $row['employee'] = $row['firstname'].' '.$row['lastname'];
                $row['content'] = str_replace("\\", "", $row['content']);
                $row['date'] = Tools::displayDate($row['date_add'], null, true);
                $row['order'] = $row['order_reference'].' '.Tools::displayDate($row['order_date']);
            }
            return $result;
        } else {
            return array();
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('printable'.$this->table_name)) {
            $this->processPrintablemp_customer_order_notes();
        }
        if (Tools::isSubmit('deleted'.$this->table_name)) {
            $this->processDeletedmp_customer_order_notes();
        }
    }

    public function togglePrintable()
    {
        $id = (int)Tools::getValue('id_note', 0);
        $db = Db::getInstance();
        $sql_update = "UPDATE "._DB_PREFIX_.$this->table_name.
            " SET printable = (1 - printable) where id_mp_customer_order_notes=".(int)$id;
        $sql_get = "select printable from "._DB_PREFIX_.$this->table_name." where id_mp_customer_order_notes = ".(int)$id;
        $db->execute($sql_update);
        return (int)$db->getValue($sql_get);
    }

    public function toggleDeleted()
    {
        $id = (int)Tools::getValue('id_note', 0);
        $db = Db::getInstance();
        $sql_update = "UPDATE "._DB_PREFIX_.$this->table_name.
            " SET deleted = (1 - deleted) where id_mp_customer_order_notes=".(int)$id;
        $sql_get = "select deleted from "._DB_PREFIX_.$this->table_name." where id_mp_customer_order_notes = ".(int)$id;
        $db->execute($sql);
        return (int)$db->getValue($sql);
    }

    public function processPrintablemp_customer_order_notes()
    {
        $id = (int)Tools::getValue('id_mp_customer_order_notes', 0);
        $db = Db::getInstance();
        $sql = "UPDATE "._DB_PREFIX_.$this->table_name.
            " SET printable = 1 - printable where id_mp_customer_order_notes=".(int)$id;
        $db->execute($sql);
    }

    public function processDeletedmp_customer_order_notes()
    {
        $id = (int)Tools::getValue('id_mp_customer_order_notes', 0);
        $db = Db::getInstance();
        $sql = "UPDATE "._DB_PREFIX_.$this->table_name.
            " SET deleted = 1 - deleted where id_mp_customer_order_notes=".(int)$id;
        $db->execute($sql);
    }
}
