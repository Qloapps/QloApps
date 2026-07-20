<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3.0-php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@qloapps.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to https://store.webkul.com/customisation-guidelines for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/license/osl-3.0-php Open Software License version 3.0
 */

/**
 * Serves the Booking Voucher PDF for a customer's own order.
 */
class PdfBookingVoucherControllerCore extends FrontController
{
    public $php_self = 'pdf-booking-voucher';
    protected $display_header = false;
    protected $display_footer = false;
    public $content_only = true;

    /** @var Order */
    protected $order;

    public function postProcess()
    {
        if (!$this->context->customer->isLogged() && !Tools::getValue('secure_key')) {
            Tools::redirect('index.php?controller=authentication&back=pdf-booking-voucher');
        }

        $idOrder = (int)Tools::getValue('id_order');
        if (!Validate::isUnsignedId($idOrder)) {
            die(Tools::displayError('The booking voucher was not found.'));
        }

        $order = new Order($idOrder);
        if (!Validate::isLoadedObject($order)) {
            die(Tools::displayError('The booking voucher was not found.'));
        }

        $secureKey = Tools::getValue('secure_key');
        if ($secureKey) {
            if ($order->secure_key !== $secureKey) {
                die(Tools::displayError('The booking voucher was not found.'));
            }
        } elseif ($order->id_customer != (int)$this->context->customer->id) {
            die(Tools::displayError('The booking voucher was not found.'));
        }

        $this->order = $order;
    }

    public function display()
    {
        $pdf = new PDF($this->order, PDF::TEMPLATE_BOOKING_VOUCHER, $this->context->smarty);
        $pdf->render();
    }
}
