{*
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
*}
<style>
    table
    {
        border-collapse: collapse;
        border: none;
        width: 100%;
        margin: 0 auto;
        font-family: sans-serif;
        background-color: #dedede;
    }
    
</style>
<br>
<hr>
<table>
    <tr>
        <td><h2>{l s='Order reference:' mod='mpcustomerordernotes'} {$id_order|escape:'htmlall':'UTF-8'}</h2></td>
        <td><h2>{l s='Customer:' mod='mpcustomerordernotes'} {$customer|escape:'htmlall':'UTF-8'}</h2></td>
    </tr>
    <tr>
        <td><h2>{l s='Date order: ' mod='mpcustomerordernotes'} {$date_order|escape:'htmlall':'UTF-8'}</h2></td>
        <td><h2>{l s='Total messages:' mod='mpcustomerordernotes'} {count($table_data)|escape:'htmlall':'UTF-8'}</h2></td>        
    </tr>
</table>




<hr>
<br>
<br>
{foreach $table_data as $row}
<hr>
<br>
<ul style="font-family: sans-serif; font-size: 1.3em;">
    <li>
        <span>{l s='Date' mod='mpcustomerordernotes'}: <strong>{$row.date|escape:'htmlall':'UTF-8'}</strong></span>
    </li>
    <li>
        <span>{l s='Employee' mod='mpcustomerordernotes'}: <strong>{$row.employee|escape:'htmlall':'UTF-8'}</strong></span>
    </li>
    <li>
        <span>{l s='Message' mod='mpcustomerordernotes'}: <strong>{$row.content|escape:'htmlall':'UTF-8'}</strong></span>
    </li>
</ul>
<br>
{/foreach}