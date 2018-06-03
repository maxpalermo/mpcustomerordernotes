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

class MpCustomerOrderNotesObjectModel extends ObjectModel
{
    /** @var int Id employee code */
    public $id_employee;
    /** @var int Id order code */
    public $id_order;
    /** @var date Date message */
    public $date_add;
    /** @var string Message content */
    public $content;
    /** @var int Deleted flag */
    public $deleted;
    /** @var int id language */
    public $id_lang;
    /** @var int id shop */
    public $id_shop;
    /** @var int Printable flag */
    public $printable;
    /** @var int Chat module */
    public $chat;
    /** @var MpCustomerOrderNotes Object module */
    private $module;
    /** @var String Table name */
    private $table_name;
    /** @var int Tot notes */
    private $tot_notes;

    public static $definition = array(
        'table' => 'mp_customer_order_notes',
        'primary' => 'id_mp_customer_order_notes',
        'multilang' => false,
        'fields' => array(
            'id_employee' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => false,
            ),
            'id_order' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
            'content' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isAnything',
                'required' => true,
            ),
            'id_lang' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'deleted' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'printable' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
            'chat' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'required' => true,
            ),
        ),
    );

    public function __construct($module, $id = null, $id_employee = null, $id_lang = null, $id_shop = null)
    {
        if (!$id_shop) {
            $this->id_shop = (int)Context::getContext()->shop->id;
        } else {
            $this->id_shop = (int)$id_shop;
        }
        if (!$id_lang) {
            $this->id_lang = Context::getContext()->language->id;
        } else {
            $this->id_lang = (int)$id_lang;
        }
        if (!$id_employee) {
            if (!isset(Context::getContext()->employee)) {
                $this->id_employee = (int)Tools::getValue('id_employee');
            } else {
                $this->id_employee = Context::getContext()->employee->id;
            }
        } else {
            $this->id_employee = (int)$id_employee;
        }
        if (!$this->deleted) {
            $this->deleted = 0;
        }
        if (!$this->printable) {
            $this->printable = 0;
        }
        if (!$this->chat) {
            $this->chat = 0;
        }
        $this->link = Context::getContext()->link;

        parent::__construct($id, $this->id_lang, $this->id_shop);
        $this->module = $module;
        $this->table_name = 'mp_customer_order_notes';
        $this->className = 'AdminMpCustomerOrderNotes';
        $this->smarty = Context::getContext()->smarty;
    }

    public static function installSQL($module)
    {
        $sql = array();
        $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_customer_order_notes` (
            `id_mp_customer_order_notes` int(11) NOT NULL AUTO_INCREMENT,
            `id_employee` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `date_add` datetime NOT NULL,
            `content` text NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `deleted` boolean NOT NULL,
            `printable` boolean NOT NULL,
            `chat` boolean NOT NULL,
            PRIMARY KEY  (`id_mp_customer_order_notes`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mp_customer_order_notes_attachments` (
            `id_mp_customer_order_notes_attachments` int(11) NOT NULL AUTO_INCREMENT,
            `id_mp_customer_order_notes` int(11) NOT NULL,
            `id_order` int(11) NOT NULL,
            `link` varchar(255) NOT NULL,
            PRIMARY KEY  (`id_mp_customer_order_notes_attachments`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
        foreach ($sql as $query) {
            try {
                if (Db::getInstance()->execute($query) == false) {
                    $module->addError(Db::getInstance()->getMsgError());
                    return false;
                }
            } catch (Exception $ex) {
                PrestaShopLoggerCore::addLog('Install SQL error '.$ex->getCode().' '.$ex->getMessage());
            }
        }
        return true;
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

    public function getTable()
    {
        //<!--Get filters-->
        $submitReset = (int)Tools::isSubmit('submitReset'.$this->table_name);
        $submitFilterTable = (int)Tools::getValue('submitFilter'.$this->table_name, 1);
        //$page = (int)Tools::getValue('page', 1);
        $pagination = (int)Tools::getValue('select_pagination', 20);
        $date = Tools::getValue($this->table_name."Filter_date_add", array());
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

        $currentIndex = $this->link->getAdminLink('AdminOrders').
            '&id_order='.Tools::getValue('id_order').
            '&vieworder';

        $helperList = new HelperList();
        $helperList->bootstrap = true;
        $helperList->actions = array();
        $helperList->currentIndex = $currentIndex;
        $helperList->identifier = 'id_mp_customer_order_notes';
        $helperList->no_link = true;
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
        $helperList->token = Tools::getAdminTokenLite('AdminOrders');
        $helperList->title = $this->l('Total notes:');
        $helperList->table = 'mp_customer_order_notes';

        $list = $this->getList($submitReset, $date_start, $date_end, $employee);
        $helperList->listTotal = count($list);
        $this->tot_notes = count($list);
        
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
            'printable' => array(
                'type' => 'bool',
                'align' => 'center',
                'width' => 'auto',
                'title' => $this->l('Printable', 'mpcustomerordernotes'),
                'search' => false,
                'active' => 'status',
                'ajax' => true,
            ),
            'chat' => array(
                'type' => 'bool',
                'align' => 'center',
                'width' => 'auto',
                'title' => $this->l('chat', 'mpcustomerordernotes'),
                'search' => false,
                'active' => 'status',
                'ajax' => true,
            ),
            'attachments' => array(
                'type' => 'bool',
                'align' => 'left',
                'width' => 'auto',
                'title' => $this->l('Att.', 'mpcustomerordernotes'),
                'search' => false,
                'float' => true,
            ),
        );

        return $field_list;
    }

    public function getList($reset = true, $date_start = '', $date_end = '', $employee = '')
    {
        $id_order = (int)Tools::getValue('id_order', 0);
        if (!$id_order) {
            $id_order = $this->id_order;
        }
        $db = Db::getInstance();
        $sql = new DbQueryCore();
        $sql->select('n.*')
            ->select('e.firstname')
            ->select('e.lastname')
            ->from('mp_customer_order_notes', 'n')
            ->innerJoin('employee', 'e', 'e.id_employee=n.id_employee')
            ->where('n.id_order='.(int)Tools::getValue('id_order'))
            ->where('n.deleted = 0')
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
        }
        if ($this->chat) {
            $sql->where('chat=1');
        }
        $result = $db->executeS($sql);
        if ($result) {
            foreach ($result as &$row) {
                $row['id'] = $row['id_mp_customer_order_notes'];
                $row['employee'] = $row['firstname'].' '.$row['lastname'];
                $row['content'] = str_replace("\\", "", $row['content']);
                $row['date'] = Tools::displayDate($row['date_add'], null, true);
                $row['attachments'] = $this->getAttachments($row['id']);
            }
            return $result;
        } else {
            return array();
        }
    }

    public function getAttachments($id)
    {
        $obj = new MpCustomerOrderNotesAttachmentsObjectModel($this->module);
        $att = $obj->getAttachments(0, $id);
        if ($att) {
            $total = count($att);
        } else {
            $total = 0;
        }
        $this->smarty->assign(
            array(
                'total' => (int)$total,
                'id_attachment' => (int)$id,
                'id_order' => 0,
            )
        );
        return $this->smarty->fetch($this->module->getAdminTemplatePath().'attachment_cell.tpl');
    }

    public function getAttachmentsByIdOrder($id_order)
    {
        $obj = new MpCustomerOrderNotesAttachmentsObjectModel($this->module);
        $att = $obj->getAttachments($id_order, 0);
        if ($att) {
            $total = count($att);
        } else {
            $total = 0;
        }
        $this->smarty->assign(
            array(
                'total' => (int)$total,
                'id_attachment' => 0,
                'id_order' => (int)$id_order,
            )
        );
        return $this->smarty->fetch($this->module->getAdminTemplatePath().'attachment_cell.tpl');
    }


    public function getTotNotes()
    {
        return (int)$this->tot_notes;
    }

    private function l($message)
    {
        return $this->module->l($message, 'MpCustomerOrderNotesObjectModel');
    }
}
