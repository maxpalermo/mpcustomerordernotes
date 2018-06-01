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
    .clickable:hover
    {
        cursor: pointer;
    }
</style>
{if $total>0}
    {assign var=badge_color value='#72C279'}
{else}
    {assign var=badge_color value='#aaaaaa'}
{/if}
<span class="badge clickable" style="background-color: {$badge_color}" id_order="{$id_order}" id_attachment="{$id_attachment}" onclick="javascript:getAttachment(this);">
    <i class="icon icon-paperclip"></i>
    <strong>{$total}</strong>
</span>
<div style="display: none;"></div>