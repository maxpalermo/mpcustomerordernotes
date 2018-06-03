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

$current_path = getcwd();
$prestashop_path = preg_replace('/modules\/mpcustomerordernotes\/ajax$/', '', $current_path);
$module_path = preg_replace('/ajax$/', '', $current_path);

require_once $prestashop_path.'/config/config.inc.php';
require_once $prestashop_path.'/init.php';
require_once $module_path.'/mpcustomerordernotes.php';

if (Tools::encrypt('AdminOrders') != Tools::getValue('security_key', '')) {
    die("Token not valid");
}
if (Tools::isSubmit('ajax') && Tools::getValue('action', '') == 'printCustomerOrderNote') {
    $module = new MpCustomerOrderNotes();
    $module->printCustomerOrderNote();
}
if (Tools::isSubmit('ajax') && Tools::getValue('action', '') == 'printChat') {
    $module = new MpCustomerOrderNotes();
    $module->printCustomerChat();
}
