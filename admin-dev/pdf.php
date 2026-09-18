<?php
/*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

/**
 * @deprecated 1.5.0
 * This file is deprecated, please use AdminPdfController instead
 */
Tools::displayFileAsDeprecated();

if (!Context::getContext()->employee->id) {
    Tools::redirectAdmin('index.php?controller=AdminLogin');
}

$function_array = array(
    'pdf' => 'generateInvoicePDF',
    'id_order_slip' => 'generateOrderSlipPDF',
    'id_delivery' => 'generateDeliverySlipPDF',
    'delivery' => 'generateDeliverySlipPDF',
    'invoices' => 'generateInvoicesPDF',
    'invoices2' => 'generateInvoicesPDF2',
    'slips' => 'generateOrderSlipsPDF',
    'deliveryslips' => 'generateDeliverySlipsPDF',
    'id_supply_order' => 'generateSupplyOrderFormPDF'
);

$pdf_controller = new AdminPdfController();
foreach ($function_array as $var => $function) {
    if (isset($_GET[$var])) {
        $pdf_controller->{'process'.$function}();
        exit;
    }
}

exit;
