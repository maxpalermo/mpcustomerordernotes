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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{assign var=background_color value="#ababab"}
{assign var=font_big value="18pt"}

<style>
    h1 {
        background-color: white;
        padding-bottom: 5px;
        font-size: 1.2em;
        font-weight: bold;
        color: #0061a7;
        border-bottom: 1px solid #666666;
    }
    
    .title {
        font-weight: bold;
        font-size: 0.9em;
        padding-bottom: 3px;
        border-bottom: 1px solid #aaaaaa;
    }
    
    #table-header {
        border: none;
        border-collapse: collapse;
        width: 100%;
    }
    
    #table-header > tbody > tr.header {
        background-color: #222222;
        border: none;
        border-bottom: 1px solid #aaaaaa;
        padding: 5px;
        font-size: 1.2em;
        font-weight: bold;
        color: #0061a7;
    }
    
    #table-header > tbody > tr.data-sheet {
        background-color: white;
        padding: 8px;
        font-size: 1em;
        font-weight: lighter;
    }
    
    #products {
        border-collapse: collapse;
        width: 100%;
    }
    
    #products > tbody > tr {
        font-size: 0.8em;
        font-weight: lighter;
    }
    
    #products > tbody > tr.titles > td {
        background-color: #ababab;
        font-size: 0.9em;
        font-weight: bold;
        text-align: center;
        padding-bottom: 3px;
        border: 1px solid #232323;
    }
    .med-data
    {
        font-size: 16pt;
        font-weight: bold;
    }
    .big_data
    {
        font-size: 20pt;
        font-weight: bold;
    }
    .border-right-white
    {
        border-right: 1px solid white;
    }
    .border-right-grey
    {
        border-right: 1px solid white;
    }
</style>
<div style="min-height: 110px;">
    
</div>
<table id="table-header" style="width: 100%; font-size: 12pt;">
    <thead>
        <tr style="text-align: left; font-weight: bold; background-color: {$background_color|escape:'htmlall':'UTF-8'}; color: white;">
            <th style="border-right: 1px solid white;">{l s='Payment Method' mod='mpcustomerordernotes'}</th>
            <th>{l s='Carrier' mod='mpcustomerordernotes'}</th> 
        </tr>
    </thead>
    <tbody>
        <tr style="text-align: left; font-weight: lighter; font-size: 12pt;">
            <td style="font-size: {$font_big}; border-right: 1px solid #cccccc;">{$payments.payment_method|escape:'htmlall'}</td>
            <td style="font-size: {$font_big}}">{$payments.carrier|escape:'htmlall'}</td>
        </tr>
        <tr style="background-color: white;">
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table id="table-header" style="width: 100%; font-size: 12pt;">
    <thead>
        <tr style="text-align: left; font-weight: bold; background-color: {$background_color|escape:'htmlall':'UTF-8'}; color: white;">
            <th style="border-right: 1px solid white;">{l s='DELIVERY ADDRESS' mod='mpcustomerordernotes'}</th>
            <th>{l s='INVOICE ADDRESS' mod='mpcustomerordernotes'}</th> 
        </tr>
    </thead>
    <tbody>
        <tr style="text-align: left; font-weight: lighter; font-size: 12pt;">
            <td style="border-right: 1px solid #cccccc;">{$address_delivery}</td>
            <td>{$address_invoice}</td>
        </tr>
        <tr style="background-color: white;">
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
<table id='products' style="border-collapse: collapse; border-color: #888888; border-width: 1px; font-size: 10pt">
    <tbody>
        <tr style="text-align: center; font-weight: bold; background-color: {$background_color|escape:'htmlall':'UTF-8'}; color: white; text-transform: uppercase;">
            <td style="width: 10%;">{{{l s='img' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 15%;">{{{l s='reference' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 43%;">{{{l s='name' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 5%;">{{{l s='qty' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 7%;">{{{l s='stock' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 10%;">{{{l s='price' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
            <td style="width: 10%;">{{{l s='total' mod='mpcustomerordernotes'}|strtoupper}|escape:'htmlall':'UTF-8'}</td>
        </tr>
    {assign var=i value=0}
    {foreach $products as $product}
    {assign var=i value=$i+1}
        <tr style="background-color: white; color: #121212; font-weight: lighter; border-bottom: 1px solid #0079cc; {if $i is even}background-color: #f0f0f0;{/if} font-size: 12pt;">
            <td style="width: 10%;"><img src="{$product.image_url|escape:'htmlall':'UTF-8'}" style="height: 48px;"></td>
            <td style="width: 15%;text-align: left;"><strong>{$product.product_reference|escape:'htmlall'}</strong></td>
            <td style="width: 43%;text-align: left;">
                {$product.product_name|escape:'htmlall'}
                {if $product.customization}
                    <ul>
                    {foreach $product.customization as $custom}
                        <li>
                        {$custom.title|escape:'htmlall':'UTF-8'}
                        <br>
                        {if $custom.type==0}
                            <img src="{$custom.value|escape:'htmlall':'UTF-8'}">
                        {else}
                            <strong>{$custom.value|escape:'htmlall':'UTF-8'}</strong>
                        {/if}
                        </li>
                    {/foreach}
                    </ul>
                    <br>
                {/if}
            </td>
            <td style="width: 5%;text-align: right; font-weight: bold;">{$product.product_quantity|escape:'htmlall'}</td>
            {if $product.product_stock<0}
                <td style="width: 7%;text-align: right; font-weight: bold; color: red;">{$product.product_stock|escape:'htmlall'}</td>
            {else}
                <td style="width: 5%;text-align: right; font-weight: lighter; color: green;">{$product.product_stock|escape:'htmlall'}</td>
            {/if}
            <td style="width: 10%;text-align: right;">
                {displayPrice price=$product.unit_price_tax_incl}
                {if $product.product_price_discount>20}
                    <br>
                    <span style="color: red; font-size: 0.8em; font-weight: bold;">({$product.product_price_discount|string_format:"%.2f"} %)</span>
                {/if}
            </td>
            <td style="width: 10%;text-align: right;font-weight: bold;">{displayPrice price=$product.total_price_tax_incl}</td>
        </tr>
                
    {/foreach}
    </tbody>
</table>
            
{if $messages}
    <h1>{l s='ORDER MESSAGES' mod='mpcustomerordernotes'}</h1>
    {foreach $messages as $row}
    <hr>
    <br>
    <ul style="font-family: sans-serif; font-size: 1.3em;">
        <li>
            <small><span>{l s='Date' mod='mpcustomerordernotes'}: <strong>{$row.date|escape:'htmlall':'UTF-8'}</strong></span></small>
        </li>
        <li>
            <small><span>{l s='Employee' mod='mpcustomerordernotes'}: <strong>{$row.employee|escape:'htmlall':'UTF-8'}</strong></span></small>
        </li>
        <li>
            <span>{l s='Message' mod='mpcustomerordernotes'}: <strong>{$row.content|escape:'htmlall':'UTF-8'}</strong></span>
        </li>
    </ul>
    <br>
    {/foreach}
{/if}
