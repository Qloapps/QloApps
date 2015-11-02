/*
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function(){
    var handler = new PaymentOptionHandler();
    
    if (!!$.prototype.fancybox)
        $("a.iframe").fancybox({
            'type': 'iframe',
            'width': 600,
            'height': 600
        });

    $('p.payment_module').on('click', function(event){
        handler.selectOption($(this));
        return;
    });

    $('#confirmOrder').on('click', function(event){
        /* Avoid any further action */
        event.preventDefault();
        event.stopPropagation();

        if (handler.checkTOS() === false)
        {
            var to_display = $('<div/>').html(aeuc_tos_err_str).text();
            alert(to_display);
            return;
        }
        if (aeuc_has_virtual_products === true && handler.checkVirtualProductRevocation() === false)
        {
            var to_display = $('<div/>').html(aeuc_virt_prod_err_str).text();
            alert(to_display);
            return;
        }
        if (handler.selected_option === null) {
            var to_display = $('<div/>').html(aeuc_no_pay_err_str).text();
            alert(to_display);
            return;
        }
        if (handler.submitForm() === false) {
            var to_display = $('<div/>').html(aeuc_submit_err_str).text();
            alert(to_display);
            return;
        }
        return;
    });

});

var PaymentOptionHandler = function() {

    this.selected_option = null;

    this.selectOption = function(elem) {
        if (typeof elem === 'undefined' || elem.hasClass('payment_selected')) {
            return;
        }
        if (this.selected_option !== null) {
            this.unselectOption();
        }
        this.selected_option = elem;
        this.selected_option.addClass('payment_selected');
        this.selected_option.children('a:first').children('.payment_option_selected:first').fadeIn();
    };

    this.unselectOption = function() {
        this.selected_option.children('a:first').children('.payment_option_selected:first').fadeOut();
        this.selected_option.removeClass('payment_selected');
    };

    /* Return array with all payment option information required */
    this.submitForm = function()
    {
        if (typeof this.selected_option !== 'undefined' && this.selected_option !== null && this.selected_option.hasClass('payment_selected'))
        {
            var form_to_submit = this.selected_option.next('.payment_option_form').children('form:first');
            if (typeof form_to_submit !== 'undefined') {
                form_to_submit.submit();
                return true;
            }
        }
        return false;
    };

    this.checkTOS = function() {

        if ($('#cgv').prop('checked')) {
            return true;
        }

        return false;
    };

    this.checkVirtualProductRevocation = function() {
        if ($('#revocation_vp_terms_agreed').prop('checked')) {
            return true;
        }

        return false;
    }
};
