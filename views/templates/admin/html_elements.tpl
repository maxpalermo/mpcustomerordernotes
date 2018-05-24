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
{if $element.type == 'badge'}
    <span 
        class="badge" 
        style="{if $element.background}
                    background-color: {$background|escape:'htmlall':'UTF-8'};
                {/if}
                {if $element.color}
                    color: {$element.color|escape:'htmlall':'UTF-8'};
                {/if}"
    >{$value}</span>
{else} {if $element.type == 'button'}
{else} {if $element.type == 'link'}
{else} {if $element.type == 'image'}
{else} {if $element.type == 'option'}
{else} {if $element.type == 'select'}
{else} {if $element.type == 'span'}
{else} {if $element.type == 'text'}
{else} {if $element.type == 'textarea'}
{/if}
<div class="panel">
    <div class="panel-heading">
        <i class='icon icon-list'></i>
        &nbsp;
        {l s='Error report' mod='mpstock'}
        &nbsp;
        <span class="badge">
            {$content_total|escape:'htmlall':'UTF-8'}
        </span>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <textarea name="import_errors" id="import_errors" rows="20">
                    {$content_area|escape:'htmlall':'UTF-8'}
                </textarea>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <button type="button" class="btn btn-default pull-right" onclick="javascript:saveReport();">
            <i class="process-icon-save"></i>
            <span>{l s='Save' mod='mpstock'}</span>
        </button>
    </div>
</div>
<script type="text/javascript">
    function saveReport()
    {
        $("<a />", {
          // if supported , set name of file
          download: "report_" + $.now() + ".txt",
          // set `href` to `objectURL` of `Blob` of `textarea` value
          href: URL.createObjectURL(
            new Blob([$("#import_errors").val()], {
              type: "text/plain"
            }))
        })
        // append `a` element to `body`
        // call `click` on `DOM` element `a`
        .appendTo("body")[0].click();
        // remove appended `a` element after "Save File" dialog,
        // `window` regains `focus` 
        $(window).one("focus", function() {
          $("a").last().remove()
        })
    }
</script>