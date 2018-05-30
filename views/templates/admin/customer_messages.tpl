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
    .checkmark {
        position: relative;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
        display: inline-block;
    }
    .labelmark {
        position: relative;
        top: 0;
        left: 0;
        height: 25px;
        background-color: transparent;
        display: inline-block;
    }
</style>

<div class="row">
    <div class="panel">
        <div class="panel-heading" style="overflow: hidden; height: auto;">
            <button class="btn btn-default" type="button" id='icon-customer-notes' onclick="javascript:panelToggle();">
                <i class="icon icon-caret-down" ></i>
            </button>
            &nbsp;
            {l s='Order notes' mod='mpcustomerordernotes'} : <span class="badge" id='tot_cust_notes'>{$tot_notes|escape:'htmlall':'UTF-8'}</span>
            <span>
                <button type="button" class="btn btn-success pull-right" onclick='javascript:printOrder();'style="font-size: 1.0em; margin: 10px;">
                    <i class="icon icon-print"></i>&nbsp;{l s='Print order' mod='mpcustomerordernotes'}
                </button>
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
                <div class="form-check-inline">
                    <table>
                        <tr>
                            <td>
                                <strong>{l s='Print this message on order report' mod='mpcustomerordernotes'}&nbsp;&nbsp;</strong>
                            </td>
                            <td>
                                <input type="checkbox" class="checkmark" value="0" id="printable_check">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-default pull-right" onclick="$('#mp-customer-notes-add').toggle();">
                        <i class="icon icon-times" style="color: #A94442;"></i>&nbsp;{l s='Cancel' mod='mpcustomerordernotes'}
                    </button>
                    <button type="button" class="btn btn-default pull-right" onclick='javascript:saveCustomerOrderNote();'>
                        <i class="icon icon-plus" style="color: #0AAF00;"></i>&nbsp;{l s='Save' mod='mpcustomerordernotes'}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        fixTableNotes();
    });
    String.prototype.isEmpty = function() {
        return (this.length === 0 || !this.trim());
    };

    function fixTableNotes()
    {
        var tbl_notes = $('table.mp_customer_order_notes');
        $(tbl_notes).find('tbody tr').each(function(){
            var cell = $(this).find('td:nth-child(5)>a');
            $(cell).attr('onclick', 'javascript:togglePrintable(this);');
            $(cell).attr('href', '');
        });
    }

    function togglePrintable(button)
    {
        if (confirm("{l s='Are you sure you want to toggle this option?' mod='mpcustomerordernotes'}")) {
            var id = Number($(button).closest('tr').find('td:nth-child(1)').text());
            $.ajax({
                type: 'post',
                data:
                {
                    id_note: id,
                    ajax: true,
                    action: 'togglePrintable'
                },
                success: function(response)
                {
                    //Thanks to Ernest Marcinko <http://codecanyon.net/user/anago/portfolio>
                    response = response.replace(/^\s*[\r\n]/gm, "");
                    response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
                    response = JSON.parse(response);
                    
                    //Refresh table
                    if (response.result == 1) {
                        $(button).find('i.icon-remove').addClass('hidden').css('color', '#E08F95');
                        $(button).find('i.icon-check').removeClass('hidden').css('color', '#72C279');
                    } else {
                        $(button).find('i.icon-remove').removeClass('hidden').css('color', '#E08F95');
                        $(button).find('i.icon-check').addClass('hidden').css('color', '#72C279');
                    }
                    
                },
                error: function()
                {

                }
            });
        }
    }

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
        $('#mp-customer-notes-add').toggle();
        if($('#mp-customer-notes-add').is(':visible'))
        {
            $('#mp_customer_order_note').focus();
        }
    }
    function printCustomerOrderNote()
    {
        window.open("{$currentindex}printReport.php?ajax&action=printCustomerOrderNote&id_order={$id_order|escape:'htmlall':'UTF-8'}", "Report");
    }
    function printOrder()
    {
        window.open("{$currentindex}printOrder.php?ajax&action=printOrder&id_order={$id_order|escape:'htmlall':'UTF-8'}", "{l s='Order' mod='mpcustomerordernotes'} {$id_order}");
    }
    function saveCustomerOrderNote()
    {
        var id_order = '{$id_order|escape:'htmlall':'UTF-8'}';
        var id_employee = '{$id_employee|escape:'htmlall':'UTF-8'}';
        var date_add = new Date();
        var date_json = date_add.toJSON();
        var dates = date_json.split('T');
        var date_day = dates[0];
        var date_hour = dates[1].replace(/.$/,'');
        var date_hour = date_hour.replace(/\.\d*/,'');
        var date_send = date_day+' '+date_hour;
        var content = $('#mp_customer_order_note').val();
        var printable = $('#printable_check').is(':checked')?1:0;

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
                printable: printable,
                ajax: true,
                action: 'addCustomerOrderMessage'
            },
            success: function(response)
            {
                //Thanks to Ernest Marcinko <http://codecanyon.net/user/anago/portfolio>
                response = response.replace(/^\s*[\r\n]/gm, "");
                response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
                response = JSON.parse(response);
                
                //Refresh table
                if (response.result == 1) {
                    $.growl.notice({
                        title: '{l s='Operation done' mod='mpcustomerordernotes'}',
                        message: '{l s='Message saved.' mod='mpcustomerordernotes'}'
                    });
                    refreshTableCustomerNotes();
                }
                
            },
            error: function()
            {

            }
        });
    }
    function refreshTableCustomerNotes()
    {
        $.ajax({
            type: 'post',
            data:
            {
                ajax: true,
                action: 'refreshTableCustomerNotes'
            },
            success: function(response)
            {
                //Thanks to Ernest Marcinko <http://codecanyon.net/user/anago/portfolio>
                response = response.replace(/^\s*[\r\n]/gm, "");
                response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
                $('#mp-customer-notes').html(response);
                var tot = $('#form-mp_customer_order_notes .panel-heading>.badge').text();
                $('#tot_cust_notes').text(tot);
                fixTableNotes();
                return false;
            },
            error: function()
            {

            }
        });
        return '';
    }
</script>