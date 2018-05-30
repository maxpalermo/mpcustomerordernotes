{*
* 2007-2016 PrestaShop
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
*  @author Massimiliano Palermo <info@mpsoft.it>
*  @copyright  2018 Digital Solutions®
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<style>
    h1 {
        background-color: white;
        padding-bottom: 5px;
        font-size: 1.2em;
        font-weight: bold;
        color: #0061a7;
        border-bottom: 1px solid #666666;
    }
</style>
{assign var=background_color value="#bcbcbc"}
<table style="width: 100%">
    <tr>
	<td style="width: 40%">
            {if $logo_path}
                <img src="{$logo_path|escape:'htmlall':'UTF-8'}"/>
            {/if}
	</td>
	<td style="width: 60%; text-align: right;">
            <span style="font-size: 30pt; font-weight: bold; color: #006699;">
                {l s='ORDER' mod='mpcustomerordernotes'}: {$order->id|escape:'html':'UTF-8'}
            </span>
            <br>
            <span style="font-size: 20pt; font-weight: bold;">
                {l s='CUSTOMER ID' mod='mpcustomerordernotes'}: {$order->id_customer|escape:'html':'UTF-8'}
            </span>
            <br>
            <span style="font-size: 20pt; font-weight: bold;">
                {l s='DATE' mod='mpcustomerordernotes'}: {Tools::displayDate($order->date_add, null, true)|escape:'htmlall'}
            </span>
	</td>
    </tr>
</table>
