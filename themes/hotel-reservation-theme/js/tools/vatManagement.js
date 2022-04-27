/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function() {
    vat_number();
    $(document).on('input', '#company, #company_invoice', function(){
        vat_number();
    });
});

function vat_number() {
    if ($('#company').length && ($('#company').val() != '')) {
        $('#vat_number, #vat_number_block').show();
    } else  {
        $('#vat_number, #vat_number_block').hide();
    }

    if ($('#company_invoice').length && ($('#company_invoice').val() != '')) {
        $('#vat_number_block_invoice').show();
    } else {
        $('#vat_number_block_invoice').hide();
    }
}
