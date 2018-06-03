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
        height: 24px;
        background-color: transparent;
        display: inline-block;
    }
    /** PROGRESS BAR **/
    #progress-wrp {
      border: 1px solid;
      border-color: #60ba68;
      padding: 1px;
      position: relative;
      height: 32px;
      border-radius: 3px;
      margin: 0 auto;
      margin-top: 8px;
      text-align: left;
      background: #fff;
      box-shadow: inset 1px 3px 6px rgba(0, 0, 0, 0.12);
    }

    #progress-wrp .progress-bar {
      height: 100%;
      border-radius: 3px;
      background-color: #72C279;
      width: 0;
      box-shadow: inset 1px 1px 10px rgba(0, 0, 0, 0.11);
    }

    #progress-wrp .status {
      top: 3px;
      left: 50%;
      position: absolute;
      display: inline-block;
      color: #000000;
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
                <button type="button" class="btn btn-danger pull-right" onclick='javascript:printChat();'style="font-size: 1.0em; margin: 10px;">
                    <i class="icon icon-print"></i>&nbsp;{l s='Print chat' mod='mpcustomerordernotes'}
                </button>
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
        <div id="mp-customer-notes" style="display: none;">
            <div class="row">
                <div class="col-md-12" id="tbl_custom_order_note">
                    {$customer_order_table}
                </div>
            </div>
            <div class="row" id="tbl-attachments" style="display: none;">
                <div class="col-md-6">
                    
                </div>
            </div>
        </div>
        <form id="form_notes" method="post">
        <div class="panel-body" id = 'mp-customer-notes-add' style="display: none;">
            <div class="row">
                <div class="form-group">
                    <label>{l s='Note text' mod='mpcustomerordernotes'}</label>
                    <textarea id="mp_customer_order_note" rows="5"></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class='form-group'>
                            <label for='printable_switch'>{l s='Print this message on order report' mod='mpcustomerordernotes'}</label>
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="printable_switch" id="printable_switch_on" value="1">
                                <label for="printable_switch_on" class="radioCheck">Sì</label>
                                <input type="radio" name="printable_switch" id="printable_switch_off" value="0" checked="checked">
                                <label for="printable_switch_off" class="radioCheck">No</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class='form-group'>
                            <label for='chat_switch'>{l s='This message is from chat' mod='mpcustomerordernotes'}</label>
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="chat_switch" id="chat_switch_on" value="1">
                                <label for="chat_switch_on" class="radioCheck">Sì</label>
                                <input type="radio" name="chat_switch" id="chat_switch_off" value="0" checked="checked">
                                <label for="chat_switch_off" class="radioCheck">No</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for='note_attachment'>{l s='Attachment' mod='mpcustomerordernotes'}</label>
                            <input type="file" class="file btn btn-primary" value="" id="attachment_note" style="width: 100%;">

                            <div id="progress-wrp">
                                <div class="progress-bar"></div>
                                <div class="status">0%</div>
                            </div>

                            <input type="hidden" id="hidden_id_upload" value="0">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for='note_attachment'>{l s='Attachments' mod='mpcustomerordernotes'}</label>
                            <ul id="attachments_list" class="list-group">
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="button" class="btn btn-default pull-right" onclick="$('#mp-customer-notes-add').toggle();">
                        <i class="icon icon-times" style="color: #A94442;"></i>&nbsp;{l s='Close' mod='mpcustomerordernotes'}
                    </button>
                    <button type="button" class="btn btn-default pull-right" onclick='javascript:saveCustomerOrderNote();'>
                        <i class="icon icon-plus" style="color: #0AAF00;"></i>&nbsp;{l s='Save' mod='mpcustomerordernotes'}
                    </button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<input type="hidden" id="auto-open" value="0">
<script type="text/javascript">
    /** UPLOAD CLASS **/
    var Upload = function (file) {
        this.file = file;
    };

    Upload.prototype.getType = function() {
        return this.file.type;
    };
    Upload.prototype.getSize = function() {
        return this.file.size;
    };
    Upload.prototype.getName = function() {
        return this.file.name;
    };
    Upload.prototype.doUpload = function () {
        var that = this;
        var formData = new FormData();

        // add assoc key values, this will be posts values
        formData.append("file", this.file, this.getName());
        formData.append("upload_file", true);
        $.ajax({
            type: "POST",
            url: "{$currentindex}uploadAttachment.php?ajax&action=uploadAttachment&id_order={$id_order|escape:'htmlall':'UTF-8'}",
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                myXhr.upload.onprogress = function (e) {
                    that.progressHandling(event);
                };
                /**
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', that.progressHandling, false);
                }
                **/
                return myXhr;
            },
            success: function (data) {
                data = filterResponse(data);
                var a = $('<a></a>')
                    .addClass('ref')
                    .attr('href', '{$currentindex}upload'+data.href)
                    .attr('target', '_blank')
                    .text(data.name);
                var b = $('<a></a>')
                    .attr('onclick', 'javascript:removeAttachment(this);')
                    .attr('filename', data.href)
                    .attr('filetitle', data.name)
                    .attr('file_ext', data.ext)
                    .addClass('btn btn-default')
                    .append($('<i></i>').addClass('icon icon-trash'))
                $('#attachments_list')
                    .append($('<li></li>')
                    .addClass("list-group-item")
                    .append($(b))
                    .append('&nbsp;')
                    .append($(a)));
            },
            error: function (error) {
                // handle error
            },
            async: true,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            timeout: 60000
        });
    };

    Upload.prototype.reset = function (event) {
        var progress_bar_id = "#progress-wrp";
        // update progressbars classes so it fits your code
        $(progress_bar_id + " .progress-bar").css("width", "0");
        $(progress_bar_id + " .status").text("0%");
        wait(500);
    };

    Upload.prototype.progressHandling = function (event) {
        var percent = 0;
        var position = event.loaded || event.position;
        var total = event.total;
        var progress_bar_id = "#progress-wrp";
        if (event.lengthComputable) {
            percent = Math.ceil(position / total * 100);
        }
        // update progressbars classes so it fits your code
        $(progress_bar_id + " .progress-bar").css("width", +percent + "%");
        $(progress_bar_id + " .status").text(percent + "%");
    };
    /** END UPLOAD CLASS **/

    /** UPLOAD CHANGE HANDLER **/
    $("#attachment_note").on("change", function (e) {
        var file = $(this)[0].files[0];
        var upload = new Upload(file);

        // maby check size or type here with upload.getSize() and upload.getType()
        /** Reset progressbar */
        upload.reset();
        /** Execute upload  **/
        upload.doUpload();
    });
    /** END HANDKER **/


    /** PROTOTYPE STRING ISEMPTY **/
    String.prototype.isEmpty = function() {
        return (this.length === 0 || !this.trim());
    };

    function getAttachment(span)
    {
        var id_attachment = $(span).attr('id_attachment');
        $.ajax({
            type: "post",
            url: "{$currentindex}ajax/ajaxGetAttachments.php",
            data:
            {
                id_attachment: id_attachment,
                ajax: true,
                action: "getAttachments",
                security_key: '{$security_key|escape:'htmlall':'UTF-8'}'
            },
            success: function(data)
            {
                var response = filterResponse(data);
                $('#tbl-attachments').fadeIn().find('div').html(response.html);
            },
            error: function(data)
            {
                console.log("error:", data);
            }
        });
    }

    function wait(ms){
        var start = new Date().getTime();
        var end = start;
        while(end < start + ms) {
            end = new Date().getTime();
        }
    }

    function removeAttachment(button)
    {
        if (!confirm("{l s='Remove selected attachment?' mod='mpcustomerordernotes'}")) {
            return false;
        }
        var filename = $(button).attr('filename');
        $.ajax({
            type: "post",
            url: "{$currentindex}ajax/ajaxRemoveAttachment.php",
            data:
            {
                ajax: true,
                security_key: '{$security_key|escape:'htmlall':'UTF-8'}',
                action: 'removeAttachment',
                filename: filename,
                id_order: {$id_order|escape:'htmlall':'UTF-8'}
            },
            success: function(data)
            {
                $(button).closest('li').remove();
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    function fixTableNotes()
    {
        var tbl_notes = $('table.mp_customer_order_notes');
        var cell = {};
        $(tbl_notes).find('tbody tr').each(function(){
            cell = $(this).find('td:nth-child(5)>a');
            $(cell).attr('onclick', 'javascript:toggleCellValue(this);');
            $(cell).attr('href', '');
            cell = $(this).find('td:nth-child(6)>a');
            $(cell).attr('onclick', 'javascript:toggleCellValue(this);');
            $(cell).attr('href', '');

        });
    }

    function toggleCellValue(button)
    {
        if (confirm("{l s='Are you sure you want to toggle this option?' mod='mpcustomerordernotes'}")) {
            var id = Number($(button).closest('tr').find('td:nth-child(1)').text());
            var idx = $(button).closest('td').index();
            var cells = [
                'id',
                'date',
                'employee',
                'message',
                'printable',
                'chat',
                'attachments'
            ];

            var cell = cells[idx];

            $.ajax({
                type: 'post',
                url: '{$mpcustomerordernotes_ajax}ajaxToggleCellValue.php',
                data:
                {
                    id_note: id,
                    cell: cell,
                    ajax: true,
                    action: 'toggleCellValue',
                    security_key: '{$security_key|escape:'htmlall':'UTF-8'}'
                },
                success: function(data)
                {
                    response = filterResponse(data);
                    
                    //Refresh table
                    if (response.result == 1) {
                        $(button).find('i.icon-remove').addClass('hidden').css('color', '#E08F95');
                        $(button).find('i.icon-check').removeClass('hidden').css('color', '#72C279');
                    } else {
                        $(button).find('i.icon-remove').removeClass('hidden').css('color', '#E08F95');
                        $(button).find('i.icon-check').addClass('hidden').css('color', '#72C279');
                    }
                    
                },
                error: function(data)
                {
                    console.log("error:", data);
                }
            });
        }
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
                success: function(data)
                {
                    response = filterResponse(data);
                    
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

    function toggleChat(button)
    {
        if (confirm("{l s='Are you sure you want to toggle this option?' mod='mpcustomerordernotes'}")) {
            var id = Number($(button).closest('tr').find('td:nth-child(1)').text());
            $.ajax({
                type: 'post',
                url: '{$mpcustomerordernotes_ajax}ajaxToggleCellValue.php',
                data:
                {
                    id_note: id,
                    ajax: true,
                    action: 'toggleChat',
                    security_key: '{$security_key|escape:'htmlall':'UTF-8'}'
                },
                success: function(data)
                {
                    response = filterResponse(data);
                    
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

    function filterResponse(response, json = true)
    {
        //Thanks to Ernest Marcinko <http://codecanyon.net/user/anago/portfolio>
        response = response.replace(/^\s*[\r\n]/gm, "");
        response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
        if (json) {
            return JSON.parse(response);
        } else {
            return response;
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
        window.open("{$currentindex}ajax/ajaxPrintReport.php?ajax&action=printCustomerOrderNote&id_order={$id_order|escape:'htmlall':'UTF-8'}&security_key={$security_key|escape:'htmlall':'UTF-8'}", "Report");
    }
    function printChat()
    {
        window.open("{$currentindex}ajax/ajaxPrintReport.php?ajax&action=printChat&id_order={$id_order|escape:'htmlall':'UTF-8'}&security_key={$security_key|escape:'htmlall':'UTF-8'}", "Report");
    }
    function printOrder()
    {
        window.open("{$currentindex}ajax/ajaxPrintOrder.php?ajax&action=printOrder&id_order={$id_order|escape:'htmlall':'UTF-8'}&security_key={$security_key|escape:'htmlall':'UTF-8'}", "{l s='Order' mod='mpcustomerordernotes'} {$id_order|escape:'htmlall':'UTF-8'}");
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
        var printable = $('input[name="printable_switch"]:checked').val();
        var chat = $('input[name="chat_switch"]:checked').val();
        var attachments = $('#attachments_list li');
        var links = new Array();
        $(attachments).each(function(){
            var att_link = {
                'filename' : $(this).find('a').attr('filename'),
                'filetitle' : $(this).find('a').attr('filetitle'),
                'file_ext' : $(this).find('a').attr('file_ext'),
            };
            links.push(att_link);
        });

        if (String(content).isEmpty()) {
            $.growl.warning({
                title: '{l s='No message to write' mod='mpcustomerordernotes'}',
                message: '{l s='Please insert a message before save.' mod='mpcustomerordernotes'}'
            });
            return false;
        }

        $.ajax({
            type: 'post',
            url: '{$ajaxAddMessage}',
            data:
            {
                security_key: '{$security_key|escape:'htmlall':'UTF-8'}',
                id_order: id_order,
                id_employee: id_employee,
                date_add: date_send,
                content: content,
                printable: printable,
                chat: chat,
                attachments: links,
                ajax: true,
                action: 'addCustomerOrderMessage'
            },
            success: function(response)
            {
                response = filterResponse(response);
                
                //Refresh table
                if (response.result == 1) {
                    $.growl.notice({
                        title: '{l s='Operation done' mod='mpcustomerordernotes'}',
                        message: '{l s='Message saved.' mod='mpcustomerordernotes'}'
                    });
                    $("#hidden_id_upload").val("0");
                    removeAttachments()
                    refreshTableCustomerNotes();
                }
            },
            error: function()
            {

            }
        });
    }
    function removeAttachments()
    {
        $('#attachments_list').html('');
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
                response = filterResponse(response, false);
                $('#tbl_custom_order_note').html(response);
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
    function fixPrintButton()
    {
        var original_btn = $('a[href="javascript:window.print()"]');
        $(original_btn).attr('href', '#').attr('onclick', 'printOrder(event)');
    }
    var auto_open = {$auto_open|escape:'htmlall':'UTF-8'};
    $(document).ready(function(){
        fixPrintButton();
        fixTableNotes();
        if (auto_open == 1) {
            $('#icon-customer-notes').click().focus();
        }
    });
</script>