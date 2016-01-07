<?php
	class HotelOrderRefundStages extends objectModel
	{
		public $id;
		public $name;

		public static $definition = array(
			'table' => 'htl_order_refund_stages',
			'primary' => 'id',
			'fields' => array(
				'name' =>	array('type' => self::TYPE_STRING),
			),
		);

		/**
		 * [getNameById :: To get name of the order cancellation (refund) stage by its id]
		 * @param  [int] $id [Id of the order refund stage]
		 * @return [string|boolean]     [If name found of the passed id returns name of the stage else returns false]
		 */
		public function getNameById($id)
		{
			$result = Db::getInstance()->getValue('SELECT `name` FROM `'._DB_PREFIX_.'htl_order_refund_stages` WHERE id='.$id);
			if ($result)
				return $result;
			return false;
		}

		/**
		 * [getOrderRefundStages :: To get all possible Order refund stages]
		 * @return [array|boolean] [if data found Returns array containing all stages information else returns false]
		 */
		public function getOrderRefundStages()
		{
			$result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'htl_order_refund_stages`');
			if ($result)
				return $result;
			return false;
		}

	}