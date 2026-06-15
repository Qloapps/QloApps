<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
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
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/

/**
 * @since 1.5
 */
class HTMLTemplatePaymentReceiptCore extends HTMLTemplate
{
    public $paymentReceipt;
    public $orderPayment;
    public $order;
    /**
     * @param OrderPaymentDetail $paymentReceipt
     * @param $smarty
     * @throws PrestaShopException
     */
    public function __construct(OrderPaymentDetail $paymentReceipt, $smarty, $bulk_mode = false)
    {
        $this->paymentReceipt = $paymentReceipt;
        $this->smarty = $smarty;
        $this->date = Tools::displayDate($this->paymentReceipt->date_add);
        $id_lang = Context::getContext()->language->id;
        $this->orderPayment = new OrderPayment((int)$this->paymentReceipt->id_order_payment);
        if (!Validate::isLoadedObject($this->orderPayment)) {
            throw new PrestaShopException('Cannot load the payment associated with this payment receipt.');
        }

        $this->order = new Order((int)$this->paymentReceipt->id_order);

        if (!Validate::isLoadedObject($this->order)) {
            throw new PrestaShopException('Cannot load the order associated with this payment receipt.');
        }

        $id_shop = (int)$this->order->id_shop;
        $this->shop = new Shop($id_shop);
        $this->orderPayment->amount = $this->paymentReceipt->amount;
        $this->title = $this->paymentReceipt->getPaymentReceiptNumberFormated($id_lang, $id_shop);
    }

    /**
     * Returns the template's HTML header
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(array(
            'header' => HTMLTemplatePaymentReceipt::l('Receipt'),
        ));

        return $this->smarty->fetch($this->getTemplate('header'));
    }

     public function getFooter()
    {
        $shop_address = $this->getShopAddress();

        $id_shop = (int)$this->shop->id;

        $this->smarty->assign(array(
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
            'free_text' => Configuration::get('PS_PAYMENT_RECEIPTS_FREE_TEXT', (int)Context::getContext()->language->id, null, $id_shop)
        ));

        return $this->smarty->fetch($this->getTemplate('footer'));
    }

    public function getContent()
    {   
        $delivery_address = $invoice_address = new Address((int)$this->order->id_address_invoice);
        $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        $formatted_delivery_address = '';

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int)$this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
        }
        
        $formattedHotelAddress = '';
        if (Module::isInstalled('hotelreservationsystem')) {
            if ($idHotel = HotelBookingDetail::getIdHotelByIdOrder($this->order->id)) {
                $objHotelBranchInfo = new HotelBranchInformation((int) $idHotel, Context::getContext()->language->id);
                $invoiceAddressPatternRules['avoid'][] = 'lastname';
                if ($idHotelAddress = $objHotelBranchInfo->getHotelIdAddress()) {
                    $objHotelAddress = new Address((int) $idHotelAddress);
                    $objHotelAddress->firstname = $objHotelBranchInfo->hotel_name;
                    $formattedHotelAddress = AddressFormat::generateAddress($objHotelAddress, $invoiceAddressPatternRules, '<br />', ' ');
                }
            }
        }
        $customer = new Customer((int)$this->order->id_customer);
        $getPaymentTypes = $this->order->getPaymentsTypes();
        $this->orderPayment->payment_type = isset($getPaymentTypes[$this->orderPayment->payment_type]) ? $getPaymentTypes[$this->orderPayment->payment_type] : null;
    
        $legal_free_text = Configuration::get('PS_PAYMENT_RECEIPTS_LEGAL_FREE_TEXT', (int)Context::getContext()->language->id, null, (int)$this->order->id_shop);

        $this->smarty->assign(array(
            'order' => $this->order,
            'payment_list' => array($this->orderPayment),
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => array('invoice' => $invoice_address, 'delivery' => $delivery_address),
            'customer' => $customer,
            'hotel_address' => $formattedHotelAddress,
            'legal_free_text' => $legal_free_text,
        ));
        
        $smarty_tpls = array(
            'style_tab' => $this->smarty->fetch($this->getTemplate('payment-receipt.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('payment-receipt.addresses-tab')),
            'payment_info_tab' => $this->smarty->fetch($this->getTemplate('payment-receipt.payment-info-tab')),
        );
        $this->smarty->assign($smarty_tpls);
        return $this->smarty->fetch($this->getTemplate('payment-receipt'));
        
    }

    public function getFilename()
    {
        return 'payment-receipt-'.sprintf('%06d', $this->paymentReceipt->id).'.pdf';
    }  

    public function getBulkFilename()
    {
        return 'payment-receipts.pdf';
    }

    protected function getPaymentType($paymentType = null)
    {
        $paymentTypeName = null;
        switch ($paymentType) {
            case OrderPayment::PAYMENT_TYPE_PAY_AT_HOTEL:
                $paymentTypeName = HTMLTemplatePaymentReceipt::l('Pay at hotel');
                break;
            case OrderPayment::PAYMENT_TYPE_ONLINE:
                $paymentTypeName = HTMLTemplatePaymentReceipt::l('Online');
                break;
            case OrderPayment::PAYMENT_TYPE_REMOTE_PAYMENT:
                $paymentTypeName = HTMLTemplatePaymentReceipt::l('Remote payment');
                break;
            default:
                $paymentTypeName = null;
                break;
        }
        return $paymentTypeName;
    }
}
