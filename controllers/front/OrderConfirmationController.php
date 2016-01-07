<?php
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

class OrderConfirmationControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'order-confirmation';
    public $id_cart;
    public $id_module;
    public $id_order;
    public $reference;
    public $secure_key;

    /**
     * Initialize order confirmation controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $this->id_cart = (int)(Tools::getValue('id_cart', 0));
        $is_guest = false;

        /* check if the cart has been made by a Guest customer, for redirect link */
        if (Cart::isGuestCartByCartId($this->id_cart)) {
            $is_guest = true;
            $redirectLink = 'index.php?controller=guest-tracking';
        } else {
            $redirectLink = 'index.php?controller=history';
        }

        $this->id_module = (int)(Tools::getValue('id_module', 0));
        $this->id_order = Order::getOrderByCartId((int)($this->id_cart));
        $this->secure_key = Tools::getValue('key', false);
        $order = new Order((int)($this->id_order));
        if ($is_guest) {
            $customer = new Customer((int)$order->id_customer);
            $redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
        }
        if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key)) {
            Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
        }
        $this->reference = $order->reference;
        if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key) {
            Tools::redirect($redirectLink);
        }
        $module = Module::getInstanceById((int)($this->id_module));
        if ($order->module != $module->name) {
            Tools::redirect($redirectLink);
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign(array(
            'is_guest' => $this->context->customer->is_guest,
            'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
            'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn()
        ));

        if ($this->context->customer->is_guest) {
            $this->context->smarty->assign(array(
                'id_order' => $this->id_order,
                'reference_order' => $this->reference,
                'id_order_formatted' => sprintf('#%06d', $this->id_order),
                'email' => $this->context->customer->email
            ));
            /* If guest we clear the cookie for security reason */
            $this->context->customer->mylogout();
        }
        $customer = $this->context->customer;
        $order = new Order($this->id_order);
        $products = $order->getProducts();

        /*By webkul to show order details properly on order history page*/
        if (Module::isInstalled('hotelreservationsystem')) 
        {
            require_once (_PS_MODULE_DIR_.'hotelreservationsystem/define.php');

            $obj_cart_bk_data = new HotelCartBookingData();
            $obj_htl_bk_dtl = new HotelBookingDetail();
            $obj_rm_type = new HotelRoomType();
            $non_requested_rooms = 0;
            $any_back_order = 0;
            if (!empty($products)) 
            {
                foreach ($products as $type_key => $type_value) 
                {
                    $product = new Product($type_value['product_id'], false, $this->context->language->id);
                    $cover_image_arr = $product->getCover($type_value['product_id']);
                    
                    if(!empty($cover_image_arr))
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $product->id.'-'.$cover_image_arr['id_image'], 'small_default');
                    else 
                        $cover_img = $this->context->link->getImageLink($product->link_rewrite, $this->context->language->iso_code."-default", 'small_default');

                    if (isset($customer->id)) 
                    {
                        $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($order->id_cart, (new Cart($order->id_cart))->id_guest, $type_value['product_id'], $customer->id);
                    }
                    else
                    {
                        $cart_bk_data = $obj_cart_bk_data->getOnlyCartBookingData($order->id_cart, $customer->id_guest, $type_value['product_id']);
                    }
                    $rm_dtl = $obj_rm_type->getRoomTypeInfoByIdProduct($type_value['product_id']);
                    /*Product price when order was created*/
                    $prod_ord_dtl = $obj_htl_bk_dtl->getPsOrderDetailsByProduct($type_value['product_id'], $order->id);
                    $unit_price_tax_excl = $prod_ord_dtl['unit_price_tax_excl'];
                    $unit_price_tax_incl = $prod_ord_dtl['unit_price_tax_incl'];

                    $cart_htl_data[$type_key]['id_product'] = $type_value['product_id'];
                    $cart_htl_data[$type_key]['cover_img']  = $cover_img;
                    $cart_htl_data[$type_key]['name']       = $prod_ord_dtl['product_name'];
                    $cart_htl_data[$type_key]['unit_price_tax_excl'] = $unit_price_tax_excl;
                    $cart_htl_data[$type_key]['unit_price_tax_incl'] = $unit_price_tax_incl;
                    $cart_htl_data[$type_key]['adult']      = $rm_dtl['adult'];
                    $cart_htl_data[$type_key]['children']   = $rm_dtl['children'];
                    foreach ($cart_bk_data as $data_k => $data_v) 
                    {
                        $date_join = strtotime($data_v['date_from']).strtotime($data_v['date_to']);

                        //work on entring refund data
                        $obj_ord_ref_info = new HotelOrderRefundInfo();
                        $ord_refnd_info = $obj_ord_ref_info->getOderRefundInfoByIdOrderIdProductByDate($this->id_order, $type_value['product_id'], $data_v['date_from'], $data_v['date_to']);
                        if ($ord_refnd_info)
                        {
                            $obj_refund_stages = new HotelOrderRefundStages();
                            $stage_name = $obj_refund_stages->getNameById($ord_refnd_info['refund_stage_id']);
                        }
                        else
                        {
                            $stage_name = '';
                            $non_requested_rooms = 1;
                        }
                        if (isset($cart_htl_data[$type_key]['date_diff'][$date_join]))
                        {
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] += 1;

                            $num_days = $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'];
                            $vart_quant = (int)$cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] * $num_days;
                            
                            $amount_tax_excl = $unit_price_tax_excl * $vart_quant;
                            $amount_tax_incl = $unit_price_tax_incl * $vart_quant;

                            $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $amount_tax_incl;
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $amount_tax_excl;
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                            if ($data_v['is_back_order'])
                                $any_back_order = 1;

                            //refund_stage
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['stage_name'] = $stage_name;
                        }
                        else
                        {
                            $num_days = $obj_htl_bk_dtl->getNumberOfDays($data_v['date_from'], $data_v['date_to']);

                            $cart_htl_data[$type_key]['date_diff'][$date_join]['num_rm'] = 1;
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['data_form'] = $data_v['date_from'];
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['data_to'] = $data_v['date_to'];
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['num_days'] = $num_days;
                            
                            $amount_tax_excl = $unit_price_tax_excl * $num_days;
                            $amount_tax_incl = $unit_price_tax_incl * $num_days;

                            $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_incl'] = $amount_tax_incl;
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['amount_tax_excl'] = $amount_tax_excl;
                            if ($data_v['is_back_order'])
                                $any_back_order = 1;
                            //refund_stage
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['stage_name'] = $stage_name;
                            $cart_htl_data[$type_key]['date_diff'][$date_join]['is_backorder'] = $data_v['is_back_order'];
                        }
                    }
                }
                $redirect_link_terms = $this->context->link->getCMSLink(new CMS(3, $this->context->language->id), null, $this->context->language->id);
                $this->context->smarty->assign('redirect_link_terms', $redirect_link_terms);
                $this->context->smarty->assign('cart_htl_data', $cart_htl_data);
                $this->context->smarty->assign('non_requested_rooms', $non_requested_rooms);

                //For Advanced Payment
                $obj_customer_adv = new HotelCustomerAdvancedPayment();
                $order_adv_dtl = $obj_customer_adv->getCstAdvPaymentDtlByIdOrder($order->id);
                if ($order_adv_dtl)
                    $this->context->smarty->assign('order_adv_dtl', $order_adv_dtl);
            }
        }

        $shw_bo_msg = Configuration::get('WK_SHOW_MSG_ON_BO');
        $bo_msg = Configuration::get('WK_BO_MESSAGE');
        $this->context->smarty->assign(array(
            'any_back_order' => $any_back_order,
            'shw_bo_msg' => $shw_bo_msg,
            'back_ord_msg' => $bo_msg,
            'order' => $order,
            'use_tax' => Configuration::get('PS_TAX'),
            'group_use_tax' => (Group::getPriceDisplayMethod($customer->id_default_group) == PS_TAX_INC),
        ));

        /*END*/
        $this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
    }

    /**
     * Execute the hook displayPaymentReturn
     */
    public function displayPaymentReturn()
    {
        if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module)) {
            $params = array();
            $order = new Order($this->id_order);
            $currency = new Currency($order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $params['total_to_pay'] = $order->getOrdersTotalPaid();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('displayPaymentReturn', $params, $this->id_module);
            }
        }
        return false;
    }

    /**
     * Execute the hook displayOrderConfirmation
     */
    public function displayOrderConfirmation()
    {
        if (Validate::isUnsignedId($this->id_order)) {
            $params = array();
            $order = new Order($this->id_order);
            $currency = new Currency($order->id_currency);

            if (Validate::isLoadedObject($order)) {
                $params['total_to_pay'] = $order->getOrdersTotalPaid();
                $params['currency'] = $currency->sign;
                $params['objOrder'] = $order;
                $params['currencyObj'] = $currency;

                return Hook::exec('displayOrderConfirmation', $params);
            }
        }
        return false;
    }

    public function setMedia()
    {
        if (Tools::getValue('ajax') != 'true')
        {
            parent::setMedia();
            $this->addCSS(_THEME_CSS_DIR_.'history.css');
            $this->addJS(_THEME_JS_DIR_.'history.js');
        }
    }
}
