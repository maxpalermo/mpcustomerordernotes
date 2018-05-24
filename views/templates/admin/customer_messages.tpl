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
    #icon-customer-notes:hover
    {
        cursor: pointer;
    }
</style>

<div class="row">
    <div class="panel">
        <div class="panel-heading" style="overflow: hidden; height: auto;">
            <button class="btn btn-default" type="button" id='icon-customer-notes' onclick="javascript:panelToggle();">
                <i class="icon icon-caret-down" ></i>
            </button>
            &nbsp;
            {l s='Order notes' mod='mpcustomerordernotes'}
            <span>
                <button type="button" class="btn btn-info pull-right" onclick='javascript:printCustomerOrderNote();'style="font-size: 1.0em; margin: 10px;">
                    <i class="icon icon-print"></i>&nbsp;{l s='Print report' mod='mpcustomerordernotes'}
                </button>
                <button type="button" class="btn btn-warning pull-right" onclick='javascript:addCustomerOrderNote();' style="font-size: 1.0em; margin: 10px;">
                    <i class="icon icon-plus"></i>&nbsp;{l s='Add new note' mod='mpcustomerordernotes'}
                </button>
            </span>
        </div>
        <div id = 'mp-customer-notes' style="display: none;">
            {$customer_order_table}
        </div>
        <div class="panel-body" id = 'mp-customer-notes-add' style="display: none;">
            <div class="row">
                <div class="form-group">
                    <label>{l s='Note text' mod='mpcustomerordernotes'}</label>
                    <textarea id="mp_customer_order_note"></textarea>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-default pull-right" onclick="$('#mp-customer-notes-add').toggle();">
                        <i class="icon icon-times" style="color: #A94442;"></i>&nbsp;{l s='Cancel' mod='mpcustomerordernotes'}
                    </button>
                    <button type="button" class="btn btn-default pull-right" onclick='javascript:saveCustomerOrderNote();'>
                        <i class="icon icon-plus" style="color: #0AAF00;"></i>&nbsp;{l s='Add new note' mod='mpcustomerordernotes'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    String.prototype.isEmpty = function() {
        return (this.length === 0 || !this.trim());
    };

    function panelToggle()
    {
        $('#mp-customer-notes').toggle();
        if($('#mp-customer-notes').is(':visible')) {
            $('#icon-customer-notes i').removeClass('icon-caret-down').addClass('icon-caret-right');
            $('#icon-customer-notes').removeClass('btn-default').addClass('btn-info');
        } else {
            $('#icon-customer-notes i').removeClass('icon-caret-right').addClass('icon-caret-down');
            $('#icon-customer-notes').removeClass('btn-info').addClass('btn-default');
        }
    }
    function addCustomerOrderNote()
    {
        console.log('toggle add');
        $('#mp-customer-notes-add').toggle();
        if($('#mp-customer-notes-add').is(':visible'))
        {
            $('#mp_customer_order_note').focus();
        }
    }
    function printCustomerOrderNote()
    {

    }
    function saveCustomerOrderNote()
    {
        var id_order = '{$id_order}';
        var id_employee = '{$id_employee}';
        var date_add = new Date();
        var date_json = date_add.toJSON();
        var dates = date_json.split('T');
        var date_day = dates[0];
        var date_hour = dates[1].replace(/.$/,'');
        var date_hour = date_hour.replace(/\.\d*/,'');
        var date_send = date_day+' '+date_hour;
        var content = $('#mp_customer_order_note').val();
        console.log (date_send);

        if (String(content).isEmpty()) {
            $.growl.warning({
                title: '{l s='No message to write' mod='mpcustomerordernotes'}',
                message: '{l s='Please insert a message before save.' mod='mpcustomerordernotes'}'
            });
            return false;
        }

        $.ajax({
            type: 'post',
            data:
            {
                id_order: id_order,
                id_employee: id_employee,
                date_add: date_send,
                content: content,
                ajax: true,
                action: 'addCustomerOrderMessage'
            },
            success: function(response)
            {
                // You can of course merge these lines
                response = response.replace(/^\s*[\r\n]/gm, "");
                response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
                // If you don't need JSON, just skip the line below
                response = JSON.parse(response);
             
                // Here the response is in the expected JSON format
                console.log(response);  // prints the response
            },
            error: function()
            {

            }
        });
    }
</script>