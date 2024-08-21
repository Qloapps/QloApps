/**
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

$(document).ready(function() {

    $('.btn-group-action .delete').on('click', function (e) {
        e.preventDefault();
        var id_cart_rule = $.trim($(this).closest('tr').find('td[data-key="id_cart_rule"]').text());
        processDelete(id_cart_rule, $(this).attr('href'))
        return false;
    });

    function processDelete(id_cart_rule, link)
    {
        confirmDelete(id_cart_rule).then((toDelete) => {
            if (toDelete) {
                window.location = link;
            }
        });

    }

    function confirmDelete(id_cart_rule)
    {
        return new Promise((resolve) => {
            $.ajax({
                type: 'POST',
                url: admin_cart_rule_tab_link,
                dataType: 'JSON',
                cache: false,
                data: {
                    ajax: true,
                    action: 'initCartRuleDeleteModal',
                    id_cart_rule: id_cart_rule
                },
                success: function(result) {
                    if (result.success && result.confirm_delete) {
                        $('#moduleConfirmDelete').remove();
                        $('#footer').next('.bootstrap').append(result.modalHtml);
                        $('#moduleConfirmDelete').modal('show');
                        $('#moduleConfirmDelete .process_delete').click(() => {
                            resolve(true);
                        });
                        $('#moduleConfirmDelete .btn-close').click(() => {
                            resolve(false);
                        });
                    }
                }
            });
        });
    }
});

function confirmDeleteBulk(form)
{
    var id_cart_rules = [];
    $(form).find('[name="cart_ruleBox[]"]:checked').each(function () {
        id_cart_rules.push($(this).val());
    });
    return new Promise((resolve) => {
        $.ajax({
            type: 'POST',
            url: admin_cart_rule_tab_link,
            dataType: 'JSON',
            cache: false,
            data: {
                ajax: true,
                action: 'initCartRuleBulkDeleteModal',
                id_cart_rules: id_cart_rules
            },
            success: function(result) {
                if (result.success && result.confirm_delete) {
                    $('#moduleConfirmDelete').remove();
                    $('#footer').next('.bootstrap').append(result.modalHtml);
                    $('#moduleConfirmDelete').modal('show');
                    $('#moduleConfirmDelete .process_delete').click(() => {
                        resolve(true);
                    });
                    $('#moduleConfirmDelete .btn-close').click(() => {
                        resolve(false);
                    });
                }
            },
        });
    });
}

function sendBulkAction(form, action)
{
    if (action.search('delete')!= -1) {
        confirmDeleteBulk(form).then((toDelete) => {
            if (toDelete) {
                submitBulkActionForm(form, action);
            }
        });
    } else {
        submitBulkActionForm(form, action);
    }
}

function submitBulkActionForm(form, action)
{
    String.prototype.splice = function(index, remove, string) {
        return (this.slice(0, index) + string + this.slice(index + Math.abs(remove)));
    };

    var form_action = $(form).attr('action');

    if (form_action.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ') == '')
        return false;

    if (form_action.indexOf('#') == -1)
        $(form).attr('action', form_action + '&' + action);
    else
        $(form).attr('action', form_action.splice(form_action.lastIndexOf('&'), 0, '&' + action));


    $(form).submit();
}

$(document).on('change', '.cart_rule_to_delete', function() {
    if ($(this).prop('checked')) {
        $('[name="cart_ruleBox[]"][value="'+$(this).val()+'"]').prop('checked', true);
    } else {
        $('[name="cart_ruleBox[]"][value="'+$(this).val()+'"]').prop('checked', false);
    }
});