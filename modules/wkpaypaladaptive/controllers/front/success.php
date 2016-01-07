<?php
class WkPaypalAdaptiveSuccessModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public function initContent()
	{
		if ($this->module->active)
        {
			parent::initContent();
			$link = new Link();
			$order_link = $link->getPageLink('order-confirmation');
			$cart = $this->context->cart;

			//If the order already exists, we need to update the order status
			if ($cart->OrderExists())
			{
				$obj_order = new Order((int)Order::getOrderByCartId($cart->id));
                $new_history = new OrderHistory();
                $new_history->id_order = (int)$obj_order->id;
                $new_history->changeIdOrderState((int)Configuration::get('PS_OS_PAYMENT'), $obj_order, true);
                $new_history->addWithemail(true);
			}
			else
			{
				//protect from error if cart already created
				if (!$this->context->cart->id)
					Tools::redirect($link->getPageLink('history'));

				$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
				$currency = $this->context->currency;
				$customer = new Customer($cart->id_customer);

				$this->module->validateOrder($cart->id, 
											 Configuration::get('PS_OS_PAYMENT'), 
											 $total, 
											 $this->module->displayName, 
											 NULL, 
											 array(), 
											 (int)$currency->id, 
											 false, 
											 $customer->secure_key
											);

			}

			Tools::redirect($order_link.'?id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$cart->secure_key);
		}
	}
}
?>