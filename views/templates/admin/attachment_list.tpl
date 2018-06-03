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
{assign var=att value=$attachments}

<div class="panel-body">
	<h3>{l s='Attachments list' mod='mpcustomerordernotes'}</h3>
	<ul class="list-group">
	{foreach $attachments as $att}
		{assign var='icon' value="{$base_path}views/img/{$att['file_ext']}.png"}
		<li class="list-group-item">
			<img src="{$icon}" style="width: 32px;">&nbsp;
			<a class="btn btn-default"
				href="javascript:removeAttachment($('#att_{$att['id_mp_customer_order_notes_attachments']}'));"      
				id="att_{$att['id_mp_customer_order_notes_attachments']}"
				filename="{$att['filename']}"
                filetitle="{$att['filetitle']}"
                file_ext="{$att['file_ext']}">
                <i class="icon icon-trash" style="color: #A94442;"></i>
			</a>
			<a href="{$att['link_path']}" target="_blank">{$att['filetitle']}</a>
		</li>
	{/foreach}
	</ul>
	<br>
	<button type="button" class="btn btn-default pull-right" onclick="javascript:$('#tbl-attachments').fadeOut();">
		<i class="icon icon-times" style="color: #A94442;"></i>
		&nbsp;
		{l s='Close' mod='mpcustomerordernotes'}
	</button>
</div>
