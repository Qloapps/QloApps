<?php
class AdminOrderRefundRequestsController extends ModuleAdminController 
{
	public function __construct()
    {
		$this->bootstrap = true;
		$this->table = 'htl_order_refund_info';
		$this->className = 'HotelOrderRefundInfo';
		$this->context = Context::getContext();
		$this->list_id = 'id';

		$this->_select = ' SUM(a.`order_amount`) AS total_order_amount, CONCAT(firstname, " ", lastname) AS cust_name';

		/*for showing status of booking with badge_danger or success*/
		$this->_select .= ' ,IF(IFNULL((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id_order=a.id_order AND (refund_stage_id=1 OR refund_stage_id=2) LIMIT 1) , \''.$this->l('Done').'\') = \''.$this->l('Done').'\', \''.$this->l('Done').'\', \''.$this->l('Pending').'\') AS req_status , IF((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id_order=a.id_order AND (refund_stage_id=1 OR refund_stage_id=2) LIMIT 1), 1, 0) badge_danger, IF((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id_order=a.id_order AND (refund_stage_id=1 OR refund_stage_id=2) LIMIT 1), 0, 1) badge_success';


		$this->_lang = $this->context->language->id;
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` cust ON (a.`id_customer` = cust.`id_customer`)';
		$this->_group = 'GROUP BY a.`id_order`';
		$this->_orderBy = 'a.id_order';
		$this->_orderWay = 'DESC';

		$this->fields_list = array(
		    'id_order' => array(
				'title' => $this->l('Id Order'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'cust_name' => array(
				'title' => $this->l('Customer Name'),
				'align' => 'center',
				'havingFilter' => true
			),
			'total_order_amount' => array(
				'title' => $this->l('Total Amount'),
				'align' => 'center',
				'type' => 'price',
				'currency' => true,
				'callback' => 'setOrderCurrency',
				'havingFilter' => true
			),
			'req_status' => array(
				'title' => $this->l('Requests Status'),
				'align' => 'center',
				'havingFilter' => true,
				'badge_danger' => true,
				'badge_success' => true
			),
		);

		/*$this->bulk_actions = array(
								'delete' => array('text' => $this->l('Delete selected'),
													'icon' => 'icon-trash',
												 'confirm' => $this->l('Delete selected items?')));
*/
		$this->addRowAction('view');
		//$this->addRowAction('delete');
		$this->identifier = 'id';
        parent::__construct();
	}

	/**
	 * [setOrderCurrency description] - A callback function for setting currency sign with amount
	 * @param [type] $echo [description]
	 * @param [type] $tr   [description]
	 */
	public static function setOrderCurrency($echo, $tr)
	{
		$refund_req_obj = new HotelOrderRefundInfo($tr['id']);
		return Tools::displayPrice($echo, (int)$refund_req_obj->id_currency);
	}

	public function initToolbar()
	{
		parent::initToolbar();
		unset($this->toolbar_btn['new']);
	}

	public function renderView()
	{
		if (!Tools::getValue('view_by_order'))
		{
			if (!($obj = $this->loadObject(true)))
				return;
	    	$id_order = $obj->id_order;
	    	$this->table = 'htl_order_refund_info';
	        $this->className  = 'HotelOrderRefundInfo';
	        $this->identifier = 'id';

	       	/*for showing status of booking with badge_danger or success*/

	       	$this->_select = ' CONCAT(firstname, " ", lastname) AS cust_name';

			$this->_select .= ' ,IF(IFNULL((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id=a.id AND a.refund_stage_id=1 OR a.refund_stage_id=2 LIMIT 1) , \''.$this->l('Done').'\') = \''.$this->l('Done').'\', \''.$this->l('Done').'\', \''.$this->l('Pending').'\') AS refund_status , IF((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id=a.id AND a.refund_stage_id=1 OR a.refund_stage_id=2 LIMIT 1), 1, 0) badge_danger, IF((SELECT a.id FROM '._DB_PREFIX_.'htl_order_refund_info WHERE id=a.id AND a.refund_stage_id=1 OR a.refund_stage_id=2 LIMIT 1), 0, 1) badge_success';
			$this->_join = ' LEFT JOIN `'._DB_PREFIX_.'customer` cust ON (a.`id_customer` = cust.`id_customer`)';
			$this->_where = 'AND a.`id_order`='.$id_order;
			$this->_group = '';
			$this->_orderBy = '';

	    	if (!Validate::isLoadedObject(new HotelOrderRefundInfo((int)$obj->id)))
	        {
	            $this->errors[] = Tools::displayError($this->l('An error occurred while updating the status for an object.')).' <b>'.$this->table.'</b> '.Tools::displayError($this->l('(cannot load object)'));
	            return;
	        }

	        $this->fields_list = array(
			    'id' => array(
					'title' => $this->l('Id'),
					'align' => 'center',
					'class' => 'fixed-width-xs'
				),
				'id_order' => array(
					'title' => $this->l('id_order'),
					'align' => 'center'
				),
				'cust_name' => array(
					'title' => $this->l('Customer Name'),
					'align' => 'center',
				),
				'order_amount' => array(
					'title' => $this->l('Amount'),
					'align' => 'center',
					'type' => 'price',
					'currency' => true,
					'callback' => 'setOrderCurrency',
				),
		        'refund_status' => array(
					'title' => $this->l('Refund Status'),
					'align' => 'center',
					'havingFilter' => true,
					'badge_success' =>true,
					'badge_danger' => true
				),
				'date_add' => array(
	                'title' => $this->l('Requested Date'),
	                'type' => 'datetime',
	            ),
			);

			$this->bulk_actions = array(
								'delete' => array('text' => $this->l('Delete selected'),
												'icon' => 'icon-trash',
												'confirm' => $this->l('Delete selected items?')));
			$this->addRowAction('view');
			$this->addRowAction('delete');
			$this->identifier = 'id';

			self::$currentIndex = self::$currentIndex.'&view_by_order=1';
	        return parent::renderList();
	    }
        else
        	return parent::renderView();

	}

	public function postProcess()
    {
		$id_order_refund = Tools::getValue('id');
        $deduction_amount = 0;

        if (!($obj_refund = $this->loadObject(true)))
			return;

		$date_from = $obj_refund->date_from;
		$date_to = $obj_refund->date_to;
		$id_cart = Cart::getCartIdByOrderId($obj_refund->id_order);
		$id_product = $obj_refund->id_product;
		$id_currency = $obj_refund->id_currency;
		$id_customer = $obj_refund->id_customer;
		$id_order = $obj_refund->id_order;
		$refund_stage_id = $obj_refund->refund_stage_id;
		$order_amount = $obj_refund->order_amount;

    	if (Tools::getValue('view_by_order')) 
        {
			if ($obj_refund->id_customer) // if not a guest
			{
				$obj_customer = new Customer($obj_refund->id_customer);
				$this->context->smarty->assign('customer_name', $obj_customer->firstname.' '.$obj_customer->lastname);
				$this->context->smarty->assign('customer_email', $obj_customer->email);
			}

			//product info
			$prod_obj = new Product($id_product, false, $this->context->language->id);
			$product_name = $prod_obj->name;

			//currency info
			$obj_currency = new Currency($id_currency);
		    $currency_iso_code = $obj_currency->iso_code;

			//room info
		    $obj_htl_cart_bk_data = new HotelCartBookingData();
		    $rooms_ids = $obj_htl_cart_bk_data->getCustomerIdRoomsByIdCartIdProduct($id_cart, $id_product, $date_from, $date_to);

		    foreach ($rooms_ids as $key_rm => $val_rm)
		    {
		    	$obj_room_info = new HotelRoomInformation($val_rm['id_room']);
		    	$rooms_names[] = $obj_room_info->room_num;
		    }
		    $obj_room_info1 = new HotelRoomInformation($rooms_ids[0]['id_room']);

			$obj_hotel_branch_info = new HotelBranchInformation($obj_room_info1->id_hotel);
			$obj_ord_refund_stage = new HotelOrderRefundStages($refund_stage_id);
			$obj_ord_refund_stages1 = new HotelOrderRefundStages();

			$all_ord_rfnd_stgs = $obj_ord_refund_stages1->getOrderRefundStages();
			$deduction_amount = Tools::convertPrice($this->calculateDeductionAmountFromRefundRules($obj_refund), new Currency($id_currency));
			
			/* In case of advance payment */
			$obj_htl_cust_adv_pay = new HotelCustomerAdvancedPayment();
	    	$htl_cust_adv_pay_info = $obj_htl_cust_adv_pay->getCstAdvPaymentDtlByIdOrder($id_order);
	    	
	    	if (isset($htl_cust_adv_pay_info) && $htl_cust_adv_pay_info)
	    	{
		    	$obj_booking_detail = new HotelBookingDetail();
		    	$obj_htl_adv_pay = new HotelAdvancedPayment();

				$book_days = $obj_booking_detail->getNumberOfDays($date_from, $date_to);
				$qty_prod = ($obj_refund->num_rooms)*$book_days;
				
				$adv_paid_amount = $obj_htl_adv_pay->getProductMinAdvPaymentAmount($id_product, $qty_prod);
	    		$way_of_payment = 'Advance Payment';
	    		
	    		$adv_paid_amount = Tools::convertPrice($adv_paid_amount, new Currency($id_currency));

				$this->context->smarty->assign('adv_paid_amount', Tools::ps_round($adv_paid_amount, 2));
	    	}
	    	else
	    		$way_of_payment = 'Full Payment';

			$this->context->smarty->assign(array(
											'refunded_amount' => $obj_refund->refunded_amount,
											'deduction_amount' => Tools::ps_round($deduction_amount, 2),
											'way_of_payment' => $way_of_payment,
											'total_amount' => Tools::ps_round($order_amount, 2),
											'curr_code' => $currency_iso_code,
											'currentStage' => $obj_ord_refund_stage,
											'room_numbers' => $rooms_names,
											'htl_name' => $obj_hotel_branch_info->hotel_name,
											'id_order_refund' => $id_order_refund,
											'product_name' => $product_name,
											'all_ord_refund_stages' => $all_ord_rfnd_stgs,
											'date_from' => $date_from,
											'date_to' => $date_to,
										));
        }

        /*If Admin update the status of the order cancellation request*/
        if (Tools::isSubmit('submitOrderCancelStage'))
        {
        	$cancel_charges_for_cust = Tools::getValue('cancellation_charge');
        	$new_stage_id = Tools::getValue('id_order_cancellation_stage');
        	
        	if (!$new_stage_id)
        		Tools::redirectAdmin(self::$currentIndex.'&view_by_order=1&id='.$id_order_refund.'&viewhtl_order_refund_info&token='.$this->token);

        	if ($cancel_charges_for_cust != "" && !Validate::isFloat($cancel_charges_for_cust))
        		$this->errors[] = Tools::displayError('Refunded Amount should be Numeric.');
        	else if ($way_of_payment == 'Advance Payment' && $cancel_charges_for_cust > $adv_paid_amount)
        		$this->errors[] = Tools::displayError('Refunded Amount should be less then Advance Paid amount for the order.');
        	else if ($cancel_charges_for_cust != "" && $cancel_charges_for_cust > $order_amount)
        		$this->errors[] = Tools::displayError('Refunded Amount should be less then Total Amount.');

        	if (!count($this->errors))
        	{
	        	if ($new_stage_id == 3)
	        	{
	        		$refunded_amount = 0;

	        		if ($cancel_charges_for_cust != "" && $way_of_payment == 'Advance Payment')
						$refunded_amount = $adv_paid_amount - $cancel_charges_for_cust;	
	        		else if ($cancel_charges_for_cust != "")
	        			$refunded_amount = $order_amount - $cancel_charges_for_cust;
	        		else if ($way_of_payment == 'Advance Payment')
	        			$refunded_amount = $adv_paid_amount - $deduction_amount;
	        		else
	        			$refunded_amount = $order_amount - $deduction_amount;

	        		$obj_ord_refund_info = new HotelOrderRefundInfo($id_order_refund);
	        		$obj_ord_refund_info->refund_stage_id = $new_stage_id;
	        		$obj_ord_refund_info->refunded_amount = $refunded_amount;
	        		$obj_ord_refund_info->save();
	        	}
	        	else
	        	{
	        		$obj_ord_refund_info = new HotelOrderRefundInfo($id_order_refund);
	        		$obj_ord_refund_info->refund_stage_id = $new_stage_id;
	        		$obj_ord_refund_info->save();
	        	}
	        	if ($obj_ord_refund_info->id)
	        	{
	        		$obj_htl_booking = new HotelBookingDetail();
	        		$updated_booking = $obj_htl_booking->updateOrderRefundStatus($id_order, $date_from, $date_to, $rooms_ids);

	        		$obj_htl_cart_booking = new HotelCartBookingData();
	        		$updated_cart_booking = $obj_htl_cart_booking->updateOrderRefundStatus($id_cart, $date_from, $date_to, $rooms_ids);

	        	}

	        	$id_shop = Context::getContext()->shop->id;

	        	$admin_data = new EmployeeCore(1);

    		    $templateVars = array(
    		    	'{shop_name}' => Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop)),
    		    	'{history_url}' => Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop),
    		    	'{my_account_url}' => Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop),
					'{currency_sign}' => $obj_currency->sign,
					'{amount}' => $order_amount,
					'{date_from}' => $date_from,
					'{date_to}' => $date_to,
					'{cust_name}' => $obj_customer->firstname.' '.$obj_customer->lastname,
					'{cust_email}' => $obj_customer->email,
					'{product_name}' => $product_name,
					'{admin_email}' => $admin_data->email,
				);
				if ($new_stage_id == 1)
					$template_name = 'request_processed';
    		    else if ($new_stage_id == 2)
	        		$template_name = 'accepted_conf';
	        	else if ($new_stage_id == 3) // if new stage is refunded
	        	{
	        		$templateVars['{cancellation_charge}'] = $order_amount-$refunded_amount;
    		    	$templateVars['{refunded_amount}'] = $refunded_amount;
	        		$template_name = 'refund_conf';
	        	}
	        	else if ($new_stage_id == 4)
	        		$template_name = 'reject_conf';

	        	$to = $obj_customer->email;

	        	if (!$this->sendOrderCancellationStatusMail($templateVars, $to, $template_name))
	        	{
	        		$this->errors = Tools::displayError('Some error occurred while sending mail to the customer');
	        	}
	        	else
	        	{
	        		Tools::redirectAdmin(self::$currentIndex.'&view_by_order=1&conf=4&id='.$id_order_refund.'&viewhtl_order_refund_info&token='.$this->token);
	        	}
        	}
        }
        parent::postProcess();
    }

    /**
     * [calculateDeductionAmountFromRefundRules :: Calculate the deduction amount from the order with respect to refund rules]
     * @param  [type] $date_from    [Rooms booking start date]
     * @param  [type] $total_amount [Total ccost of this room's booking]
     * @return [type]               [deduction amount for order cancellation]
     */
    public function calculateDeductionAmountFromRefundRules($obj_refund)
    {
    	$adv_paid_amount = 0;
    	$deduct_amount_val = 0;
    	$way_of_payment = 'fullPayment';
    	$deduction_amount = 0;
		
		$obj_booking_detail = new HotelBookingDetail();
		$obj_htl_adv_pay = new HotelAdvancedPayment();

    	$obj_htl_cust_adv_pay = new HotelCustomerAdvancedPayment();
    	$htl_cust_adv_pay_info = $obj_htl_cust_adv_pay->getCstAdvPaymentDtlByIdOrder($obj_refund->id_order);
    	
    	if (isset($htl_cust_adv_pay_info) && $htl_cust_adv_pay_info['total_paid_amount'] < $htl_cust_adv_pay_info['total_order_amount'])
    		$way_of_payment = 'advancePayment';

    	$obj_refnd_rules = new HotelOrderRefundRules();
	    $refund_rules = $obj_refnd_rules->getAllOrderRefundRulesOrderByDays();
	    if ($refund_rules)
	    {
	    	foreach ($refund_rules as $k_rules => $v_rules)
			{
				$explode_date = explode(' ', $v_rules['date_add']);
				$date_request = date('Y-m-d', strtotime($explode_date[0]));
				$date1 = date_create($obj_refund->date_from);
				$date2 = date_create($date_request);
				$days_diff = date_diff($date1,$date2);
				$days_before_cancellation = (int)$days_diff->format("%a");

				if ($days_before_cancellation >= $v_rules['days'])
				{
					if ($way_of_payment == 'advancePayment')
					{
						$book_days = $obj_booking_detail->getNumberOfDays($obj_refund->date_from, $obj_refund->date_to);
						$qty_prod = ($obj_refund->num_rooms)*$book_days;
						$adv_paid_amount = $obj_htl_adv_pay->getProductMinAdvPaymentAmount($obj_refund->id_product, $qty_prod);
						$deduct_amount_val = $v_rules['deduction_value_adv_pay'];
					}
					else
						$deduct_amount_val = $v_rules['deduction_value_full_pay'];

					if ($v_rules['payment_type'] == 1)
					{
						if ($way_of_payment == 'advancePayment')
							$deduction_amount = $adv_paid_amount*($deduct_amount_val/100);
						else
							$deduction_amount = $obj_refund->order_amount*($deduct_amount_val/100);
					}
					else
					{
						$default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
						$order_tran_curr = $obj_refund->id_currency;

						if ($default_currency != $order_tran_curr)
							$deduction_amount = Tools::convertPriceFull($deduct_amount_val, new Currency($default_currency), new Currency($order_tran_curr));
						else
							$deduction_amount = $deduct_amount_val;
						//if deduction amount is more than the order total cost
						if ($way_of_payment == 'fullPayment' && ($deduction_amount > $obj_refund->order_amount))
							$deduction_amount = $obj_refund->order_amount;
						if ($way_of_payment == 'advancePayment' && ($deduction_amount > $adv_paid_amount))
							$deduction_amount = $adv_paid_amount;
					}
					break;
				}
			}
		}
		if (!$deduction_amount)
		{
			if ($way_of_payment == 'advancePayment')
			{
				$book_days = $obj_booking_detail->getNumberOfDays($obj_refund->date_from, $obj_refund->date_to);
				$qty_prod = ($obj_refund->num_rooms)*$book_days;
				$deduction_amount = $obj_htl_adv_pay->getProductMinAdvPaymentAmount($obj_refund->id_product, $qty_prod);
			}
			else
				$deduction_amount = $obj_refund->order_amount;

		}
		return $deduction_amount;
    }

    public function sendOrderCancellationStatusMail($templateVars, $to, $template_name)
    {
		$id_lang = Configuration::get('PS_LANG_DEFAULT');
        $temp_path = _PS_MODULE_DIR_.'hotelreservationsystem/mails/';
        if (Mail::Send($id_lang, $template_name, Mail::l('Order Cancellation Status', $id_lang), $templateVars, $to, null, null, null,  null, null, $temp_path, false, null, null))
        	return true;
        else
        	return false;
    }

    public function setMedia()
	{
		parent::setMedia();
		$this->addJs(_MODULE_DIR_.'hotelreservationsystem/views/js/HotelReservationAdmin.js');
		$this->addCSS(_MODULE_DIR_.'hotelreservationsystem/views/css/HotelReservationAdmin.css');
	}
}
?>